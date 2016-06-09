<?php

namespace app\models;

use app\models\base\FeedBase;
use app\models\Media;
use app\models\MQueue;
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
            if (!$this->time_created) $this->time_created = time();
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

        $item = self::find()->where($data)->one();
        $isFirstTime = false;

        // If no record
        if (!$item) {
            $item = new Feed($data);
            $item->is_active = 1;
            $isFirstTime = true;
        // Of the record is not active
        } elseif (!$item->is_active) {
            $item->is_active = 1;
        // Or leave as is
        } else {
            return true;
        }
    	
    	if ($item->save()) {
            if ($isFirstTime) self::onFirstTimeFollow($user, $reader);

    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * When some user follows another for the first time
     * 
     * @param $user
     * @param $reader
     */
    public static function onFirstTimeFollow($user, $reader) {
        if ($user->id == $reader->id) return;
        
        MQueue::compose()
            ->toUser($user)
            ->subject('Новый комментайри к вашей истории')
            ->bodyTemplate('follower.php', [
                'reader'         => $reader,
            ])
            ->send();
    }

    /**
     * Removes reader from user
     *
     * @param object $user
     * @return object $reader
     */
    public static function unfollow($user, $reader) {
    	$data = ['reader_id' => $reader->id, 'user_id' => $user->id];

        $item = self::find()->where($data)->one();

        if (!$item || !$item->is_active) {
            return true;
        } else {
            $item->is_active = 0;
            return $item->save();
        }
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
        $isEmpty = true;

        $ids = Helpers::fetchFields(self::sqlSelect('user_id', ['reader_id' => $user->id, 'is_active' => 1]), 'user_id', ['isSingle' => true]);

        if(!$ids) {
            $mediaList = [];

            if (!empty($extra['stats'])) {
                $totalItems = 0;
                $totalPages = 0;
            }
        } else {
            $cond = [
                'created_by' => ['IN', $ids],
                'type' => Media::typeStoryImage,
                'is_deleted' => false,
                'is_hidden' => false
            ];

            if ($lastTime) $cond['time_created'] = ['>', $lastTime];
            elseif ($firstTime) $cond['time_created'] = ['<', $firstTime];

            $mediaList = Media::find()->where(self::makeCondition($cond))->with('targetStory')->with('creator')->orderBy('time_created DESC')->offset(($page - 1) * $maxItems)->limit($maxItems)->all();

            if (count($mediaList)) $isEmpty = false;

            if (!empty($extra['stats'])) {
                $totalItems = Media::find()->where(self::makeCondition($cond))->count();
                $totalPages = ceil($totalItems / $maxItems);
            }

            foreach ($mediaList as $mediaItem) {
                $mediaItem->setScenario('feed');
                if ($mediaItem->targetStory) $mediaItem->targetStory->setScenario('feed');
            }
        }

        return [
                    'list'       => $mediaList,
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages,
                    'isEmpty'    => $isEmpty
               ];
    }

    /**
     * Check if one user follows another
     *
     * @param  object $user
     */
    public static function isFollowing($user, $reader) {
    	$data = ['reader_id' => $reader->id, 'user_id' => $user->id, 'is_active' => 1];

    	return self::getCount($data) ? true : false;
    }

    /**
     * Check if user has subscriptions
     * @param $reader
     * @return bool
     */
    public static function isSubscribed($reader) {
        return self::getCount(['reader_id' => $reader->id]) ? true : false;
    }

    /**
     * Lists all user's readers
     *
     * @param  object $user
     */
    public static function listUsers($user) {
        
    }
}