<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $is_deleted
 * @property integer $parent_id
 * @property integer $lk
 * @property integer $rk
 * @property integer $level
 * @property integer $thread
 * @property integer $created_by
 * @property integer $target_type
 * @property integer $target_id
 * @property string $body
 * @property string $body_jvx
 * @property integer $time_created
 * @property integer $time_updated
 */
class CommentBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'target_id', 'target_type', 'time_created', 'body'], 'required'],
            [['is_deleted', 'parent_id', 'lk', 'rk', 'level', 'thread', 'created_by', 'target_type', 'target_id', 'time_created', 'time_updated'], 'integer'],
            [['body', 'body_jvx'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_deleted' => 'Is Deleted',
            'parent_id' => 'Parent ID',
            'lk' => 'Lk',
            'rk' => 'Rk',
            'level' => 'Level',
            'thread' => 'Thread',
            'created_by' => 'Created By',
            'target_type' => 'Target Type',
            'target_id' => 'Target ID',
            'body' => 'Body',
            'body_jvx' => 'Body Jvx',
            'time_created' => 'Time Created',
            'time_updated' => 'Time Changed',
        ];
    }

    /**
     * @inheritdoc
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQueryBase(get_called_class());
    }
}
