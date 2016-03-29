<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "option".
 *
 * @property integer $id
 * @property string $name
 * @property string $default
 * @property integer $group_id
 */
class OptionBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id'], 'required'],
            [['group_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['default'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'default' => 'Default',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @inheritdoc
     * @return OptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OptionQueryBase(get_called_class());
    }
}
