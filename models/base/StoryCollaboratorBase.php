<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "story_collaborator".
 *
 * @property integer $story_id
 * @property integer $user_id
 * @property integer $permission
 * @property integer $is_confirmed
 * @property integer $time_created
 */
class StoryCollaboratorBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story_collaborator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'user_id', 'permission', 'time_created'], 'required'],
            [['story_id', 'user_id', 'permission', 'is_confirmed', 'time_created'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'story_id' => 'Story ID',
            'user_id' => 'User ID',
            'permission' => 'Permission',
            'is_confirmed' => 'Is Confirmed',
            'time_created' => 'Time Created',
        ];
    }

    public static function primaryKey() {
        return ['story_id', 'user_id', 'permission', 'is_confirmed'];
    }

    /**
     * @inheritdoc
     * @return StoryCollaboratoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoryCollaboratorQueryBase(get_called_class());
    }
}
