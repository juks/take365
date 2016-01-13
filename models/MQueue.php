<?php

namespace app\models;

use Yii;
use app\components\Helpers;
use app\models\base\MQueueBase;
use app\components\traits\TModelExtra;

/**
 * MQueue class
 */
class MQueue extends MQueueBase {
    protected $_headersArray = [
                                    "MIME-Version" => "1.0",
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
        $limit = Helpers::getParam('MQueue/sendLimit', 5);

        $messages = MQueue::find()->where(['send_me' => 1, 'is_pending' => 0])->orderBy('time_created')->all();

        foreach($messages as $message) {
            $message->deliver();
        }
    }

    /**
     * Sets message recipient
     * @param string $recipient
     */
    public function to($recipient) {
        $this->to = $recipient;

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
        $this->headers = '';
        $this->send_me = 1;

        foreach ($this->_headersArray as $header => $value) {
            if ($this->headers) $this->headers .= "\n";
            $this->headers .= $header . ': ' . $value;
        }

        if (!$this->save()) throw new \Exception("Failed to queue message!");
    }

    /**
    * Actually sends message to delivery
    */
    public function deliver() {
        $doEncode = true;

        $this->lock();

        $boundary = Helpers::randomString(6);

        $stringHeaders = $this->headers;
        if ($stringHeaders) $stringHeaders .= "\n";
        $stringHeaders .= "Content-Type: multipart/alternative;" . "\n\t" . "boundary=\"" . $boundary . "\"\n\n";

        $dataPlain  = "\n--" . $boundary . "\n";
        $dataPlain .= "Content-Type: text/plain; charset=utf-8\n";
        if ($doEncode) $dataPlain .= "Content-Transfer-Encoding: base64\n\n";
        $dataPlain .= $doEncode ? self::base64trim(base64_encode(strip_tags(trim($this->body)))) : strip_tags(trim($this->body));

        $dataHTML   = "\n\n--" . $boundary . "\n";
        $dataHTML  .= "Content-Type: text/html; charset=utf-8\n";
        if ($doEncode) $dataHTML .= "Content-Transfer-Encoding: base64\n\n";
        $dataHTML  .= $doEncode ? self::base64trim(base64_encode(trim($this->body))) : trim($$this->body);
        $dataHTML  .= "\n\n--" . $boundary . "--";

        $mailBody = $dataPlain . $dataHTML;

        $mailSubject = $doEncode ? "=?UTF-8?B?" . base64_encode($this->subject) . "?=" : $this->subject;

        if (!mail($recipient, $mailSubject, $mailBody, $stringHeaders)) {
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
}