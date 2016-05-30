<?php

namespace app\models;

use app\models\base\FeedBase;
use app\models\Media;
use Yii;
use app\components\Helpers;
use app\components\traits\TModelExtra;

/**
 * Feed class
 */
class Feed extends FeedBase {
	use TModelExtra;

	public static $maxItems = 20;
    public static $maxItemsLimit = 100;

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
     * Adds some fellow reader to user
     *
     * @param object $user
     * @return object $reader
     */
    public static function follow($user, $reader) {
    	$data = ['reader_id' => $reader->id, 'user_id' => $user->id];
    	if (self::getCount($data)) return false;

    	$item = new Feed($data);
    	if ($item->save()) {
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * Removes reader from user
     *
     * @param object $user
     * @return object $reader
     */
    public static function unfollow($user, $reader) {
    	$data = ['reader_id' => $reader->id, 'user_id' => $user->id];
    	if (!self::getCount($data)) return false;

    	self::sqlDelete($data);

    	return true;
    }

    /**
     * Returns user's feed
     *
     * @param object $user
     */
    public static function feed($user, $extra = null) {
        $page = empty($extra['page']) ? 1 : $extra['page'];
        $maxItems = empty($extra['maxItems']) ? self::$maxItems : $extra['maxItems'];
        $lastTime = empty($extra['lastTime']) ? 0 : $extra['lastTime'];
        $firstTime = empty($extra['firstTime']) ? 0 : $extra['firstTime'];

        if (!$maxItems > self::$maxItemsLimit) $maxItems = self::$maxItemsLimit;

        $totalItems = null;
        $totalPages = null;

        $ids = Helpers::fetchFields(self::sqlSelect('user_id', ['reader_id' => $user->id]), 'user_id', ['isSingle' => true]);
        $cond = [
            'created_by'    => ['IN', $ids],
            'type' 			=> Media::typeStoryImage,
            'is_deleted' 	=> false,
            'is_hidden'     => false
        ];

        if ($lastTime) $cond['time_created'] = ['>', $lastTime];
        elseif ($firstTime) $cond['time_created'] = ['<', $firstTime];

        $mediaList = Media::find()->where(self::makeCondition($cond))->with('targetStory')->with('creator')->orderBy('time_created DESC')->offset(($page - 1) * $maxItems)->limit($maxItems)->all();

        if (!empty($extra['stats'])) {
            $totalItems = Media::find()->where(self::makeCondition($cond))->count();
            $totalPages = ceil($totalItems / $maxItems);
        }

        foreach ($mediaList as $mediaItem) {
            $mediaItem->setScenario('feed');
            if ($mediaItem->targetStory) $mediaItem->targetStory->setScenario('feed');
        }

        return ['list' => $mediaList, 'totalItems' => $totalItems, 'totalPages' => $totalPages];
    }

    /**
     * Check if one user follows another
     *
     * @param  object $user
     */
    public static function isFollowing($user, $reader) {
    	$data = ['reader_id' => $reader->id, 'user_id' => $user->id];

    	return self::getCount($data) ? true : false;
    }

    /**
     * Lists all user's readers
     *
     * @param  object $user
     */
    public static function listUsers($user) {
        
    }
}