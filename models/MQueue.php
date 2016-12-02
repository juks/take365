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

    protected $_headersArray = [
                                    'MIME-Version'              => '1.0',
                                ];

    /*
    * Composes new email
    */
    public static function compose() {
        return new MQueue();
    }

    /**
     * Limit the base64-encoded string length
     * @param $data
     * @return string
     */
    public static function base64trim($data) {
        $limit = 76;
        $ln = strlen($data);

        if($ln < $limit) return $data;

        $s = 0; $n = $limit;
        $result = '';

        while(1) {
            $result .= substr($data, $s, $n) . "\n";

            if ($s + $n >= $ln) break;

            $s += $n;
            $n = ($s + $n > $ln) ? $ln - $n : $limit;
        }

        return $result;
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

        if ($this->_user) $parameters['urlUnsubscribe'] = $this->_user->urlEdit;

        $this->body = Yii::$app->view->renderFile('@app/views/email/' . $templateName, $parameters);

        return $this;
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
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
        } 

        return parent::beforeValidate();
    }

    /**
    * Puts the message into message queue
    */
    public function send() {
        if ($this->_doSkip) return false;

        $this->send_me = 1;

        if (!$this->save()) throw new \Exception("Failed to queue message!");
    }

    /**
    * Generates headers string
    */
    public function getHeadersString() {
        $result = $this->headers;

        foreach ($this->_headersArray as $header => $value) {
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

        $senderEmail = Helpers::getParam('projectRobotEmail');
        $expire = Helpers::getParam('mQueue/expire', 86400);

        // Is message expired
        if (time() - $this->time_created > $expire) {
            $this->reject();
            return;
        }

        $this->setHeader('From', Helpers::getParam('projectName') . ' mailer <' . $senderEmail . '>');
        $stringHeaders = $this->getHeadersString();

        if ($stringHeaders) $stringHeaders .= "\n";
        $boundary = '==' . Helpers::randomString(10) . '==';

        $stringHeaders .= "Content-Type: multipart/alternative;" . "\n " . "boundary=\"" . $boundary . "\"\n\n";

        $dataPlain  = "\n--" . $boundary . "\n";
        $dataPlain .= "Content-Type: text/plain; charset=utf-8\n";
        $dataPlain .= "MIME-Version: 1.0\n";
        if ($doEncode) $dataPlain .= "Content-Transfer-Encoding: base64\n";
        $dataPlain .= "\n";
        $dataPlain .= $doEncode ? self::base64trim(base64_encode(strip_tags(trim($this->body)))) : strip_tags(trim($this->body));

        $dataHTML   = "\n\n--" . $boundary . "\n";
        $dataHTML  .= "Content-Type: text/html; charset=utf-8\n";
        $dataHTML  .= "MIME-Version: 1.0\n";
        if ($doEncode) $dataHTML .= "Content-Transfer-Encoding: base64\n";
        $dataHTML .= "\n";
        $dataHTML  .= $doEncode ? self::base64trim(base64_encode(trim($this->body))) : trim($this->body);

        // Attachments
        if ($this->attach_count) {
            $items = $this->attachments;

            foreach ($items as $item) {
                $dataHTML .= "\n\n--" . $boundary . "--\n";
                $dataHTML .= "Content-Type: " . $item->resource->mime . "; name=\"" . $item->name . "\"\n";
                $dataHTML .= "Content-Disposition: inline; filename=\"" . $item->name . "\"\n";
                $dataHTML .= "Content-Transfer-Encoding: base64\n";
                $dataHTML .= "Content-ID: <" . $item->attach_id . ">\n";
                $dataHTML .= "Content-Location: " . $item->name . "\n\n";

                $dataHTML .= self::base64trim(base64_encode(file_get_contents($item->resource->fullPath)));
                $dataHTML .= "\n\n--" . $boundary . "--";
            }
        } else {
            $dataHTML  .= "\n\n--" . $boundary . "--";
        }

        $mailBody = $dataPlain . $dataHTML;

        //$mailSubject = $doEncode ? "=?UTF-8?B?" . base64_encode($this->subject) . "?=" : $this->subject;
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