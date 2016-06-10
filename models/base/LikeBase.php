<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "like".
 *
 * @property integer $target_id
 * @property integer $target_type
 * @property integer $created_by
 * @property integer $time_created
 * @property integer $is_active
 */
class LikeBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'like';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_id', 'target_type', 'created_by', 'time_created'], 'required'],
            [['target_id', 'target_type', 'created_by', 'time_created', 'is_active'], 'integer'],
            [['target_id', 'target_type', 'created_by'], 'unique', 'targetAttribute' => ['target_id', 'target_type', 'created_by'], 'message' => 'The combination of Target ID, Target Type and User ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'target_id' => 'Target ID',
            'target_type' => 'Target Type',
            'created_by' => 'User ID',
            'time_created' => 'Time Created',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * @inheritdoc
     * @return LikeQueryBase the active query used by this AR class.
     */
    public static function find()
    {
        return new LikeQueryBase(get_called_class());
    }
}
