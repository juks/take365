<?php

namespace app\models;

use app\models\base\StoryCollaboratorBase;
use app\models\Story;
use app\models\User;
use Yii;
use app\components\Ml;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\components\traits\TModelExtra;

/**
 * Feed class
 */
class StoryCollaborator extends StoryCollaboratorBase {
	use TModelExtra;

    const permOwner = 1;
    const permAuthor = 2;

    const maxCollaborators = 1;

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created   = time();
        }

        return parent::beforeValidate();
    }

    /**
     * List story collaborators
     *
     * @param object $story
     */
    public static function listCollaborators($story, $perm = self::permAuthor) {
        $ids = Helpers::fetchFields(self::sqlSelect('user_id', ['story_id' => $story->id, 'is_confirmed' =>true, 'permission' => $perm]), 'user_id', ['isSingle' => true]);

        return User::find()->where(['id' => $ids])->all();
    }

    /**
     * Add story collaborator
     *
     * @param object $story
     * @param object $user
     * @param int $perm
     */
    public static function add($story, $user, $perm = self::permAuthor) {
        if (!$story->hasPermission(Yii::$app->user, IPermissions::permAdmin)) throw new \app\components\ControllerException(Ml::t('Forbidden'));

        $data = ['story_id' => $story->id, 'user_id' => $user->id, 'permission' => $perm];
        $dataTotal = ['story_id' => $story->id];

        if (!self::getCount($data)) {
            if (self::getCount($dataTotal) >= self::maxCollaborators) throw new \app\components\ControllerException(Ml::t('Collaborator limit reached'));

            $c = new StoryCollaborator();
            $c->attributes = $data;

            if (!$c->save()) throw new \Exception('Failed to save collaborator');

            return true;
        } else {
            return false;
        }
    }

    /**
     * Confirm collaboration
     *
     * @param object $story
     * @param object $user
     * @param int $perm
     */
    public static function confirm($story, $user, $perm = self::permAuthor) {
        $cond = ['story_id' => $story->id, 'user_id' => $user->id, 'is_confirmed' => false, 'permission' => $perm];

        if (Yii::$app->user->id != $user->id) throw new \app\components\ControllerException(Ml::t('Forbidden'));

        $item = self::find()->where($cond)->one();
        if (!$item) throw new \app\components\ControllerException(Ml::t('No pending permission'));

        $item->is_confirmed = true;
        return $item->save();
    }

    /**
     * Remove story collaborator
     *
     * @param object $story
     * @param object $user
     * @param int $perm
     */
    public static function remove($story, $user, $perm = self::permAuthor) {
        if (!$story->hasPermission(Yii::$app->user, IPermissions::permAdmin) && !($user->id == Yii::$app->user->id)) throw new \app\components\ControllerException(Ml::t('Forbidden'));

        $data = ['story_id' => $story->id, 'user_id' => $user->id, 'permission' => $perm];

        if (self::getCount($data)) {
            StoryCollaborator::deleteAll($data);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user has collaborative permissions to interact with the given story
     *
     * @param object $story
     * @param object $user
     */
    public static function hasPermission($story, $user = null, $perm = self::permAuthor) {
        if (!$user) $user = Yii::$app->user;

        $data = ['story_id' => $story->id, 'user_id' => $user->id, 'is_confirmed' => true, 'permission' => $perm];

        return self::getCount($data) ? true : false;
    }

    /**
     * List stories that given user belongs as a collaborator to
     *
     * @param object $story
     * @param object $user
     */
    public static function listStories($user, $perm = self::permAuthor) {
        $ids = Helpers::fetchFields(self::sqlSelect('story_id', ['user_id' => $user->id, 'is_confirmed' => true, 'permission' => $perm]), 'story_id', ['isSingle' => true]);

        return Story::find()->where(['id' => $ids])->all();
    }
}