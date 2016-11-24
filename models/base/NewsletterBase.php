<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "newsletter".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $time_created
 * @property integer $time_updated
 * @property integer $time_sent
 * @property string $title
 * @property string $body
 */
class NewsletterBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'newsletter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'time_created', 'time_updated', 'time_sent'], 'integer'],
            [['body'], 'string'],
            [['title'], 'string', 'max' => 255]
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
            'time_sent' => 'Time Sent',
            'title' => 'Title',
            'body' => 'Body',
        ];
    }

    /**
     * @inheritdoc
     * @return NewsletterQueryBase the active query used by this AR class.
     */
    public static function find()
    {
        return new NewsletterQueryBase(get_called_class());
    }
}
