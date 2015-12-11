<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "mosaic".
 *
 * @property string $id
 * @property integer $is_ready
 * @property integer $time_created
 * @property string $data
 */
class MosaicBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mosaic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time_created'], 'required'],
            [['is_ready', 'time_created'], 'integer'],
            [['data'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_ready' => 'Is Ready',
            'time_created' => 'Time Created',
            'data' => 'Data',
        ];
    }

    /**
     * @inheritdoc
     * @return MosaicQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MosaicQueryBase(get_called_class());
    }
}
