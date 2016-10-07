<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $time_created
 * @property integer $time_updated
 * @property integer $time_published
 * @property integer $blog_id
 * @property integer $is_published
 * @property string $title
 * @property string $body
 * @property string $body_jvx
 */
class PostBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'time_created', 'time_updated', 'time_published', 'blog_id', 'is_published'], 'integer'],
            [['body', 'body_jvx'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['created_by', 'time_created', 'blog_id', 'title', 'body'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'Created By',
            'time_created' => 'Time Created',
            'time_updated' => 'Time Updated',
            'time_published' => 'Time Published',
            'blog_id' => 'Blog ID',
            'is_published' => 'Is Published',
            'title' => 'Title',
            'body' => 'Body',
            'body_jvx' => 'Body Jvx',
        ];
    }

    /**
     * @inheritdoc
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostQueryBase(get_called_class());
    }
}
