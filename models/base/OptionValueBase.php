<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "option_value".
 *
 * @property integer $target_id
 * @property integer $target_type
 * @property integer $option_id
 * @property integer $type
 * @property string $value_storable
 */
class OptionValueBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'option_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_id', 'target_type', 'option_id', 'type'], 'required'],
            [['target_id', 'target_type', 'option_id', 'type'], 'integer'],
            [['value_storable'], 'string', 'max' => 255],
            [['target_id', 'target_type', 'option_id'], 'unique', 'targetAttribute' => ['target_id', 'target_type', 'option_id'], 'message' => 'The combination of Target ID, Target Type and Option ID has already been taken.']
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
            'option_id' => 'Option ID',
            'type' => 'Type',
            'value_storable' => 'Value Storable',
        ];
    }

    /**
     * @inheritdoc
     * @return OptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OptionValueQueryBase(get_called_class());
    }
}
