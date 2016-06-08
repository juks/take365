<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "feed".
 *
 * @property integer $reader_id
 * @property integer $user_id
 * @property integer $time_created
 * @property integer $is_active
 */
class FeedBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reader_id', 'user_id', 'time_created'], 'required'],
            [['reader_id', 'user_id', 'time_created', 'is_active'], 'integer'],
            [['reader_id', 'user_id'], 'unique', 'targetAttribute' => ['reader_id', 'user_id'], 'message' => 'The combination of Feed ID and User ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reader_id' => 'Reader ID',
            'user_id' => 'User ID',
            'time_created' => 'Time Created',
        ];
    }

    /**
     * @inheritdoc
     * @return FeedQueryBase the active query used by this AR class.
     */
    public static function find()
    {
        return new FeedQueryBase(get_called_class());
    }
}
