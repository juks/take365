<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "mqueue_attach".
 *
 * @property integer $message_id
 * @property integer $attach_id
 * @property string $name
 * @property integer $time_created
 */
class MQueueAttachBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mqueue_attach';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'attach_id', 'time_created'], 'required'],
            [['message_id', 'attach_id', 'time_created'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_id' => 'Message ID',
            'attach_id' => 'Attach ID',
            'name' => 'Name',
            'time_created' => 'Time Created',
        ];
    }

    /**
     * @inheritdoc
     * @return MQueueAttachQueryBase the active query used by this AR class.
     */
    public static function find()
    {
        return new MQueueAttachQueryBase(get_called_class());
    }
}
