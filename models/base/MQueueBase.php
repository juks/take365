<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "mqueue".
 *
 * @property integer $id
 * @property integer $time_created
 * @property integer $time_sent
 * @property integer $send_me
 * @property integer $is_pending
 * @property integer $is_rejected
 * @property integer $pending_since
 * @property string $to
 * @property string $headers
 * @property string $subject
 * @property string $body
 * @property integer attach_count
 */
class MQueueBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mqueue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time_created'], 'required'],
            [['time_created', 'time_sent', 'send_me', 'is_pending', 'is_rejected', 'pending_since', 'attach_count'], 'integer'],
            [['headers', 'body'], 'string'],
            [['to', 'subject'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_created' => 'Time Created',
            'time_sent' => 'Time Sent',
            'send_me' => 'Send Me',
            'is_pending' => 'Is Pending',
            'is_rejected' => 'Is Rejected',
            'pending_since' => 'Pending Since',
            'to' => 'To',
            'headers' => 'Headers',
            'subject' => 'Subject',
            'body' => 'Body',
            'attach_count' => 'Attachments count'
        ];
    }

    /**
     * @inheritdoc
     * @return MqueueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MQueueQueryBase(get_called_class());
    }
}
