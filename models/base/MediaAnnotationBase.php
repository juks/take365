<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "MediaAnnotation".
 *
 * @property integer $media_id
 * @property integer $time_created
 * @property integer $time_updated
 * @property string $data
 * @property string $extra
 *
 */
class MediaAnnotationBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_annotation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['media_id'], 'required'],
            [['time_created', 'time_updated'], 'integer'],
            [['data', 'extra'], 'string', 'max' => 65536],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'media_id' => 'Media id',
            'time_created' => 'Time created',
            'time_updated' => 'Time updated',
            'data' => 'data',
        ];
    }

    /**
     * @inheritdoc
     * @return MediaAnnotationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaAnnotationQueryBase(get_called_class());
    }
}
