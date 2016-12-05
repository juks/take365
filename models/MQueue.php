<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\MQueueAttach;
use app\components\Helpers;
use app\models\base\MQueueBase;
use app\components\traits\TModelExtra;
use yii\base\Exception;

/**
 * MQueue class
 */
class MQueue extends MQueueBase {
    use TModelExtra;

    protected $_user;
    protected $_doSkip = false;
    protected $_optionName = '';

    protected $_headersArray = [];

    protected $_defaultHeadersArray = [
        'MIME-Version'              => '1.0',
    ];

    protected $_CIDRegister = [];

    /*
    * Composes new email
    */
    public static function compose() {
        return new MQueue();
    }

    /**
     * Process queue, send new messages
     */
    public static function processQueue() {
        $limit = Helpers::getParam('mQueue/sendLimit', 5);

        $messages = MQueue::find()->where(['send_me' => 1, 'is_pending' => 0])->orderBy('time_created')->limit($limit)->all();

        foreach($messages as $message) {
            $message->deliver();
        }
    }

    /**
     * Release messages that are pending too long
     */
    public static function releasePending() {
        $pendingTime = Helpers::getParam('mQueue/pendingTime', 3600);

        $messages = MQueue::find()->where(self::makeCondition(['is_pending' => 1, 'pending_since' => ['<', time() - $pendingTime], 'send_me' => 0]))->all();

        foreach($messages as $message) {
            $message->unlock();
        }    
    }

    /**
     * Drop old messages
     */
    public static function dropOldies() {
        $storeTime = Helpers::getParam('mQueue/storeTime', 86400 * 7);

        $oldies = MQueue::find()->where(self::makeCondition(['send_me' => 0, 'time_created' => ['<', time() - $storeTime]]))->all();

        if ($oldies) {
            foreach ($oldies as $item) {
                $item->delete();
            }
        }
    }

    /**
     * Sets message recipient
     * @param string $recipient
     */
    public function to($recipient) {
        if (is_object($recipient)) return $this->toUser($recipient);
        
        $this->to = $recipient;

        $this->save();

        return $this;
    }

    /**
     * Sets sender email (for certain cases like newsletter delivery)
     * @param $sender
     * @return $this
     */
    public function from($sender) {
        $this->from = $sender;

        return $this;
    }

    /**
     * Sets message recipient
     * @param id|object $recipient
     * @param array $extra
     */
    public function toUser($id, $extra) {
        if (is_object($id)) {
            $user = $id;
        } else {
            $user = User::getActiveUser($id);
        }

        if (!$user || !$user->email || !$user->email_confirmed || (!empty($extra['checkOption']) && !$user->getOptionValue($extra['checkOption']))) {
            $this->_doSkip = true;
        } else {
            $this->to = $user->email;
            $this->_user = $user;
            if (!empty($extra['checkOption'])) $this->_optionName = $extra['checkOption'];
        }

        $this->save();

        return $this;
    }    

    /**
     * Sets message subject
     * @param string $subject
     */
    public function subject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Sets message body
     * @param string $body
     */
    public function body($body) {
        $this->body = $body;

        return $this;
    }

    /**
     * Sets message body from template
     * @param string $body
     */
    public function bodyTemplate($templateName, $parameters = []) {
        $parameters['projectName'] = Helpers::getParam('projectName');
        $parameters['projectUrl'] = Helpers::getParam('projectUrl');

        if ($this->_user && $this->_optionName) {
            $parameters['urlUnsubscribe'] = $this->_user->getUrlUnsubscribe($this->_optionName);
        }

        $this->body = Yii::$app->view->renderFile('@app/views/email/' . $templateName, $parameters);

        return $this;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
        }

        if (count($this->_headersArray)) {
            $this->headers = '';
            foreach ($this->_headersArray as $headerName => $headerValue) {
                if ($this->headers) $this->headers .= "\n";
                $this->headers .= $headerName . ': ' .$headerValue;
            }
        }

        return parent::beforeValidate();
    }

    /**
    * Puts the message into message queue
    */
    public function send() {
        if ($this->_doSkip) return false;

        $this->send_me = 1;

        if ($this->_user && $this->_optionName) {
            $this->setHeader('X-Unsubscribe-Web', $this->_user->getUrlUnsubscribe($this->_optionName));
        }

        if (!$this->save()) throw new \Exception("Failed to queue message!");
    }

    /**
     * Sets message header
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value) {
        $this->_headersArray[$name] = $value;
    }

    /**
    * Generates headers string
    */
    public function getHeadersString() {
        $result = $this->headers;

        $headers = array_merge($this->_headersArray, $this->_defaultHeadersArray);

        foreach ($headers as $header => $value) {
            if ($result) $result .= "\n";
            $result .= $header . ': ' . $value;
        }

        return $result;
    }

    /**
    * Actually sends message to delivery
    */
    public function deliver() {
        $doEncode = true; //false;

        $this->lock();

        $senderEmail = $this->from ? $this->from : Helpers::getParam('projectRobotEmail');
        $expire = Helpers::getParam('mQueue/expire', 86400);

        // Is message expired
        if (time() - $this->time_created > $expire) {
            $this->reject();
            return;
        }

        $this->setHeader('From', Helpers::getParam('projectName') . ' mailer <' . $senderEmail . '>');
        $stringHeaders = $this->getHeadersString();

        if ($stringHeaders) $stringHeaders .= "\n";
        $doAttach = $this->attach_count ? 1 : 0;

        $boundary = ['==' . Helpers::randomString(10) . '==',
                     '==MIXED' . Helpers::randomString(5) . '=='];

        $dataPlain = '';

        $stringHeaders .= "Content-Type: multipart/alternative;\n boundary=\"" . $boundary[0] . "\"\n\n";

        if ($this->attach_count) $dataPlain .= "This is a multi-part message in MIME format.";
        $dataPlain .= "\n--" . $boundary[0] . "\n";
        $dataPlain .= "Content-Type: text/plain; charset=utf-8\n";
        $dataPlain .= "MIME-Version: 1.0\n";
        if ($doEncode) $dataPlain .= "Content-Transfer-Encoding: base64\n";
        $dataPlain .= "\n";
        $dataPlain .= $doEncode ? chunk_split(base64_encode(strip_tags(trim($this->body)))) : strip_tags(trim($this->body));

        $dataHTML = '';

        if ($this->attach_count) {
            $dataHTML   .= "\n--" . $boundary[0] . "\n";
            $dataHTML   .= "Content-Type: multipart/related;\n boundary=\"" . $boundary[1] . "\"\n\n";
        }

        $dataAttach = '';

        // Attachments
        if ($this->attach_count) {
            $items = $this->attachments;

            $dataAttach .= "\n--" . $boundary[1] . "\n";

            foreach ($items as $item) {
                $CID = $this->registerCID($item->resource);

                $dataAttach .= "Content-Type: " . $item->resource->mime . ";\n name=\"" . $item->name . "\"\n";
                $dataAttach .= "Content-Transfer-Encoding: base64\n";
                $dataAttach .= "Content-ID: <" . $CID. ">\n";
                $dataAttach .= "Content-Disposition: inline;\n filename=\"" . $item->name . "\"\n\n";

                $dataAttach .= chunk_split(base64_encode(file_get_contents($item->resource->fullPath)));
                $dataAttach .= "--" . $boundary[1] . "--";
            }

            $this->body = $this->replaceCIDStrings($this->body);
        }

        $dataHTML .= "\n--" . $boundary[$doAttach] . "\n";
        $dataHTML .= "Content-Type: text/html; charset=utf-8\n";
        if ($doEncode) $dataHTML .= "Content-Transfer-Encoding: base64\n";
        $dataHTML .= "\n";
        $dataHTML .= $doEncode ? chunk_split(base64_encode(trim($this->body))) : trim($this->body);

        $dataHTML .= $dataAttach;
        $dataHTML  .= "\n\n--" . $boundary[0] . "--";

        $mailBody = $dataPlain . $dataHTML;

        $mailSubject = $this->subject;

        // Non production environmеnt safety
        if (defined('YII_DEBUG') && YII_DEBUG) {
            $filterList = Helpers::getParam('mQueue/devEnvFilter');
            if (!$filterList) throw new \Exception('No email filter in non production environment!');

            // Mark email as sent without sending in non production environment
            if (array_search(strtolower($this->to), $filterList) === false) {
                $this->reject();
                return;
            }
        }

        if (!mail($this->to, $mailSubject, $mailBody, $stringHeaders)) {
            $this->unlock();
            throw new \Exception('Cannot send mail');
        }

        $this->markSent();
    }

    /**
     * Generates and stores a proper nice CID for given resource
     * @param $resource
     */
    public function registerCID($resource) {
        $uName = date('YmdHis', time()) . '.' . strtoupper(substr(md5($resource->id), 0, 12)) . '@' . gethostname();
        $fullname = $resource->filename;
        if ($resource->ext) $fullname .= '.' . $resource->ext;

        $this->_CIDRegister[$fullname] = $uName;

        return $uName;
    }

    /**
     * Replaces the occurrences of resources aliases with their proper CID values
     * @param $text
     * @return mixed
     */
    public function replaceCIDStrings($text) {
        return preg_replace_callback(
            '/\[\[resource:([^\]]+)\]\]/',
            function($matches) {
                if (!empty($this->_CIDRegister[$matches[1]])) {
                    return 'cid:' . $this->_CIDRegister[$matches[1]];
                } else {
                    return $matches[0];
                }
            },
            $text
        );
    }

    /**
    * Mark message as pending delivery
    */
    public function lock() {
        $this->setAttributes([
                                    'is_pending'    => 1,
                                    'pending_since' => time()
                                ]);

        $this->save();
    }

    /**
    * Mark message as not pending delivery
    */
    public function unlock() {
        $this->setAttributes([
                                    'is_pending'    => 0,
                                    'pending_since' => 0
                                ]);

        $this->save();        
    }

    /**
    * Mark message as not pending delivery
    */
    public function reject() {
        $this->setAttributes([
                                    'is_rejected'   => 1,
                                    'is_pending'    => 0,
                                    'send_me'       => 0
                                ]);

        $this->save();        
    }

    /**
    * Mark message as sent
    */
    public function markSent() {
        $this->setAttributes([
                                    'send_me'       => 0,
                                    'is_pending'    => 0,
                                    'pending_since' => 0,
                                    'time_sent'     => time()
                            ]);
        $this->save();
    }

    /**
     * Attaches object to message
     * @param $item
     * @throws \Exception
     */
    public function attach($item) {
        if (is_object($item) && get_class($item) == 'app\models\Storage') {
            if ($this->isAttached($item)) return $this; //throw new \Exception('This object is already attached');

            $attachName = !empty($item->filename) ? $item->filename . '.' . $item->ext : '@' . $item->id;
            $link = new MQueueAttach(['message_id' => $this->id, 'attach_id' => $item->id, 'name' => $attachName]);

            Helpers::transact(function() use ($link) {
                if ($link->save()) {
                    $this->attach_count ++;
                    $this->save();
                } else {
                    throw new \Exception('Failed to attach resource');
                }
            });
        } else {
            throw new \Exception('Unsupported attachment type');
        }

        return $this;
    }

    /**
     * Checks if message has an attachment with specified id
     * @param $attachmentId
     * @return bool
     */
    public function isAttached($attach) {
        return MQueueAttach::getCount(['message_id' => $this->id, 'attach_id' => $attach->id]) > 0;
    }

    /**
     * Get message attachments list
     * @return $this
     */
    public function getAttachments() {
        return $this->hasMany(MQueueAttach::className(), ['message_id' => 'id'])->with('resource')->orderBy('time_created');
    }

    /**
     * Delete this message together with it's attachments if there are any
     * @return false|int
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->attach_count) {
            MQueueAttach::deleteAll(self::makeCondition(['message_id' => $this->id]));
        }

        return parent::delete();
    }
}