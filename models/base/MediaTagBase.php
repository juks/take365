<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "media_tag".
 *
 * @property integer $id
 * @property integer $time_created
 * @property integer $count
 * @property string $name
 */
class MediaTagBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'time_created'], 'required'],
            [['id', 'time_created', 'count'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique']
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
            'count' => 'Count',
            'name' => 'Name',
        ];
    }

    /**
     * @inheritdoc
     * @return MediaTagQueryBase the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaTagQueryBase(get_called_class());
    }
}
