<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "story".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $time_deleted
 * @property integer $is_active
 * @property integer $time_created
 * @property integer $time_updated
 * @property integer $time_start
 * @property integer $time_published
 * @property integer $comments_count
 * @property integer $media_count
 * @property string $title
 * @property string $description
 * @property string $description_jvx
 * @property integer $is_featured
 * @property integer $is_complete
 */
class StoryBase extends \yii\db\ActiveRecord
{
    const statusPublic = 0;
    const statusPrivate = 1;

    protected $_validStatuses = [self::statusPublic, self::statusPrivate];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'time_created'], 'required'],
            [['created_by', 'status', 'is_deleted', 'time_deleted', 'is_active', 'time_created', 'time_updated', 'comments_count', 'time_start', 'time_published', 'media_count', 'is_featured', 'is_complete'], 'integer'],
            [['description', 'description_jvx'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['time_start', 'title', 'description'], 'safe'],
            [['description', 'description_jvx'], 'string', 'max' => 1024],
            ['status', 'in', 'range'=> [self::statusPublic, self::statusPrivate]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'User ID',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'time_deleted' => 'Time Deleted',
            'is_active' => 'Is Active',
            'time_created' => 'Time Created',
            'time_updated' => 'Time Updated',
            'time_start' => 'Time Start',
            'time_published' => 'Time Published',
            'comments_count' => 'Comments Count',
            'media_count' => 'Media Count',
            'title' => 'Title',
            'description' => 'Description',
            'description_jvx' => 'Description Jevix',
            'is_featured' => 'is_featured',
            'is_complete' => 'is_complete'
        ];
    }

    /**
     * @inheritdoc
     * @return StoryQueryBase the active query used by this AR class.
     */
    public static function find() {
        return new StoryQueryBase(get_called_class());
    }
}
