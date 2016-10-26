<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "media_tag_link".
 *
 * @property integer $tag_id
 * @property integer $media_id
 * @property integer $is_active
 * @property integer $time_published
 * @property integer $match
 */
class MediaTagLinkBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_tag_link';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'media_id'], 'required'],
            [['tag_id', 'media_id', 'is_active', 'time_published', 'match'], 'integer'],
            [['tag_id', 'media_id'], 'unique', 'targetAttribute' => ['tag_id', 'media_id'], 'message' => 'The combination of Tag ID and Media ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'media_id' => 'Media ID',
            'is_active' => 'Is Active',
            'time_published' => 'Time Published',
            'match' => 'Match ratio',
        ];
    }

    /**
     * @inheritdoc
     * @return MediaTagLinkQueryBase the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaTagLinkQueryBase(get_called_class());
    }
}
