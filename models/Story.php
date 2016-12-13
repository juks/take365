<?php

namespace app\models;

use Yii;
use app\models\base\StoryBase;
use app\components\Helpers;
use app\components\HelpersTxt;
use app\components\traits\TCheckField;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;
use app\components\traits\TComment;
use app\components\traits\TAuthor;
use app\models\StoryCollaborator;
use app\models\Media;
use app\models\mediaExtra\MediaCore;
use app\models\Like;
use app\models\mediaExtra\TMediaUploadExtra;
use app\components\Ml;
use app\components\interfaces\IPermissions;
use app\components\interfaces\IGetType;

/**
 * Story class
 */
class Story extends StoryBase implements IPermissions, IGetType {
    use TCheckField;
    use THasPermission;
    use TModelExtra;
    use TMediaUploadExtra;
    use TComment;
    use TAuthor;

    const typeId = 2;

    const statusPublic = 0;
    const statusHidden = 1;

    const dummyDaysCount = 3;

    public $calendar;
    public $yearStart;
    public $yearEnd;
    public $isDeleted;
    public $isHidden;
    public $images = null;
    public $progressData = null;

    public $monthTitle = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    public $monthTitleGen = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];

    protected static $_monthQuota = 2;

    /**
    *   Sets the Story model scenarios
    **/
    public function scenarios() {
        return [
            'default' => ['time_start', 'status', 'media_count', 'title', 'description']
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        $f =  [
            'id'        => 'id',
            'status'    => 'status',
            'isDeleted' => function() { return $this->is_deleted ? true : false; },
            'title'     => 'title',
            'url'       => function() { return $this->url; },
            'authors'   => function() { return $this->authors; },
            'progress'  => function() { return $this->progress; },
        ];

        if ($this->scenario == 'default') $f['images'] = function() { return $this->images; };

        return $f;
    }

    public static function getActiveCondition() {
        return ['status' => self::statusPublic];
    }

    /**
     * Returns public criteria
     */
	public function getIsPublic() {
        return $this->status == self::statusPublic && !$this->is_deleted;
    }

    /**
     * Returns owner id field name
     */
    public function getCreatorIdField() {
        return 'created_by';
    }

    /**
     * Returns integer type ID for this entity
     */
    public function getType() {
        return self::typeId;
    }

    /**
    * Returns story with given ID only if it is available for current user
    *
    * @param int $storyId
    **/
    public static function getActiveItem($storyId) {
        $story = self::findOne($storyId);
        $user = Yii::$app->user;

        if ($story && $story->hasPermission($user, IPermissions::permRead)) {
            return $story;
        }

        return null;
    }

    /**
    * Checks the model permission
    *
    * @param object $user
    * @param int $permission
    **/
    public function checkPermission($user, $permission = IPermissions::permWrite) {
        $isAuthor = $this->created_by == $user->id;

        if ($permission == IPermissions::permRead && $this->getIsPublic()) return true;
        if ($permission == IPermissions::permComment && $this->getIsPublic()) return true;
        if (!$this->is_deleted) {
            if ($isAuthor) return true;
            if ($permission == IPermissions::permWrite && StoryCollaborator::hasPermission($this, $user)) return true;
        } elseif ($permission == IPermissions::permAdmin && $isAuthor) {
            return true;
        } elseif ($permission == IPermissions::permRead && $isAuthor) {
            return true;
        }

        return false;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->title)                                      $this->title          = 'Без названия';
            if (!$this->time_created)                               $this->time_created   = time();
            if (!$this->time_start)                                 $this->time_start     = time();
            if ($this->status == self::statusPublic)                $this->time_published = time();
            if ($this->scenario != 'import' && !$this->created_by)  $this->created_by = Yii::$app->user->id;

        } else {
            $this->time_updated = time();
            if ($this->status == self::statusPublic && !$this->time_published) $this->time_published = time();
        }

        if (!$this->_oldAttributes['description'] !== $this->description) $this->description_jvx = HelpersTxt::simpleText($this->description);

        return parent::beforeValidate();
    }

    /**
     * Checks user story quota
     *
     * @param  object $user
     * @return string $type
     */
    public static function checkQuota($user = null, $type = 'month') {
        if (!$user) $user = Yii::$app->user;

        $cnt = self::getCount(['created_by' => $user->id, 'time_created' => ['>=', time() - 86400]]);

        return $cnt <= self::$_monthQuota;
    }

    /**
    * Checks if the given date is valid for this story (fits within one year span)
    * @param  string $date
    */
    public function isValidDate($date) {
        if (!$date) return false;

        $dtDate = new \DateTime($date);
        $dtStart = new \DateTime('@' . $this->time_start);
        $interval = date_diff($dtStart, $dtDate);

        if (!$interval->invert && $interval->days >= 0 && $interval->days <= 365 || $interval->invert && $interval->h <= 24 && $interval->days <= 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if given date is empty
     * @param $date
     * @return mixed
     */
    public function isEmptyDate($date) {
        return Media::find()->where(['target_id' => $this->id, 'target_type' => self::typeId, 'date' => $date, 'is_deleted' => 0])->count() == 0;
    }

    /**
    * Forms story URL
    */
    public function getUrl() {
        return $this->author->url . '/story/' . $this->id;
    }

    /**
    * Forms story comments section
    */
    public function getUrlComments() {
        return $this->url . '#comments';
    }

    /**
    * Forms day URL
    */
    public function getUrlDay($date) {
        return $this->url . '/' . $date;
    }

    /**
    * Returns authors
    */
    public function getAuthors() {
        $a = [];

        $author = User::find()->where(User::getActiveCondition())->andWhere(['id' => $this->created_by])->with('userpic')->one();
        if ($author) $a[] = $author; //['username' => $author->username, 'url' => $author->url];

        return $a;
    }

    /**
    * Fetch progress data
    */
    public function getProgress() {
        if ($this->progressData === null) $this->calculateProgress();
        return $this->progressData;
    }

    /**
     * Sets time_start according to the given date
     * @param $date
     */
    public function setStartDate($date) {
        $dt = new \DateTime($date);
        $dtNow = new \DateTime();
        $interval = date_diff($dt, $dtNow);
        if ($interval->days > 30) throw new \Exception(Ml::t('Time difference exceeds limit', 'story'));

        $this->time_start = $dt->getTimestamp();
    }

    /**
    * Returns story progress information
    */
    public function calculateProgress() {
        $totalDays      = 365;

        $yearStart = date('Y', $this->time_start);
        $monthStart = date('n', $this->time_start);
        $yearEnd = $yearStart + 1;

        // Important leap thing. Leap years supported since 2016
        $isLeapStart = date("L", mktime(1, 0, 0, 1, 1, $yearStart)) == 1;
        $isLeapEnd = date("L", mktime(1, 0, 0, 1, 1, $yearEnd)) == 1;

        if ($yearStart > 2012 && ($isLeapStart && $monthStart <= 2 || ($isLeapEnd && $monthStart > 1))) $totalDays++;

        if ($this->images === null) $this->fetchImages();

        $imagesCount    = $this->media_count;

        $lastTime       = $this->images ? strtotime($this->images[0]['date']) : $this->time_start;
        $delayDays      = intval((time() - $lastTime) / 86400);
        $passedDays     = intval((time() - $this->time_start) / 86400);
        $percentsComplete = sprintf('%2.1f', (($imagesCount / $totalDays) * 100));
        if ($percentsComplete >= 100) $percentsComplete = 100;

        $this->progressData = [
                    'dateStart'             => date('Y-m-d', $this->time_start),
                    'dateEnd'               => date('Y-m-d', $this->time_start + $totalDays * 86400),
                    'percentsComplete'      => $percentsComplete,
                    'isComplete'            => $percentsComplete == 100,
                    'isOutdated'            => $passedDays > 365,
                    'passedDays'            => $passedDays,
                    'totalImages'           => $imagesCount ?: 0,
                    'totalImagesTitle'      => Helpers::countCase($imagesCount, 'изображений', 'изображения', 'изображание'),
                    'totalDays'             => $totalDays,
                    'delayDays'             => $delayDays,
                    'delayDaysTitle'        => Helpers::countCase($delayDays, 'дней', 'дня', 'день'),
                    'delayDaysMakeSense'    => $delayDays > 3 && $passedDays <= 365,
                ];

        if ($delayDays <= 365 && !$percentsComplete != 100) $this->progressData['delayDays'] = $delayDays;
    }

    /**
     * Images relation
     */
    public function fetchImages($extra = []) {
        if ($this->images === null) {
            $mo = Media::getMediaOptions('storyImage');
            $limit = !empty($extra['imageLimit']) ? $extra['imageLimit'] : null;
            $result = $this->hasMany(Media::className(), ['target_id' => 'id', 'target_type' => 'type'])
                                 ->where(['type' => $mo[Media::mediaTypeId], 'is_deleted' => 0])
                                 ->with('targetStory');

            if (!Yii::$app->user->isGuest) $result = $result->with('isLiked');

            $result = $result->orderBy('date DESC')
                ->limit($limit)
                ->all();

            if (empty($extra['returnOnly'])) $this->images = $result;
        }

        return $result;
    }

    // DEPRECATED
    public function fetchImagesCount($extra = []) {

    }

    /**
    * Returns story title if any or default value
    */
    public function getTitleFilled() {
        return $this->title ? $this->title : 'Без названия';
    }

    /**
    * Formats story data for the shorm mode display
    */
    public function formatShort($extra = []) {
        $this->fetchImages($extra);
        $this->calculateProgress();

        foreach($this->images as $image) {
            $image->urlDay = $this->getUrlDay($image['date']);
        }

        $this->isDeleted = $this->is_deleted;
        $this->isHidden  = $this->status != self::statusPublic;
    }

    /**
    * Formats story data for user page display
    */
    public function format($extra = []) {
        $user = Yii::$app->user;
        $creator = $this->author;

        $timezone = new \DateTimeZone($creator->defaultTimezone);
        $now = new \DateTime('now', $timezone);

        $offset = $now->getOffset();
        $offsetDiff = $offset - date('Z');

        $this->calendar = [];
        $this->fetchImages($extra);
        $this->calculateProgress();
        $canUpload = $this->hasPermission(Yii::$app->user, IPermissions::permWrite);

        $dateDict = [];
        $imageIds = [];
        $likesHash = [];

        foreach ($this->images as $image) {
            $dateDict[$image['date']] = $image;
            $imageIds[] = $image['id'];
        }

        // Checking likes statistics
        if (!$user->isGuest && $imageIds) {
            $likes = Like::find()->where(Like::makeCondition(['target_id' => ['IN', $imageIds], 'target_type' => Media::typeId, 'created_by' => $user->id, 'is_active' => 1]))->all();
            if ($likes) $likesHash = Helpers::makeDict($likes, 'target_id');
        }

        $dateIterator = new \DateTime('now');
        $dateIterator->setTimezone($timezone);
        $dateUploadFrom = new \DateTime('now');
        $dateUploadFrom->setTimezone($timezone);

        $dateTarget = new \DateTime('@' . $this->time_start);
        $dateTarget->setTimezone($timezone);

        $daysDiff = $dateIterator->diff($dateTarget)->format('%d');
        if ($daysDiff > 365) $daysDiff = 365;

        // Add sample dummy days
        if ($daysDiff < self::dummyDaysCount) {
            date_add($dateIterator, date_interval_create_from_date_string((self::dummyDaysCount - $daysDiff) . ' day'));
        }

        $blankSpace = true;
        $step = date_interval_create_from_date_string('1 day');

        while (true) {
            $date       = $dateIterator->format('Y-m-d');
            $year       = $dateIterator->format('Y');
            $month      = $dateIterator->format('m');
            $monthDay   = $dateIterator->format('j');

            if (!empty($dateDict[$date])) {
                $drop = [
                            'id'            => $dateDict[$date]['id'],
                            'date'          => $date,
                            'image'         => ['url' => $dateDict[$date]['t']['squareCrop']['200']['url'], 'width' => 200, 'height' => 200],
                            'imageLarge'    => ['url' => $dateDict[$date]['t']['squareCrop']['400']['url'], 'width' => 400, 'height' => 400],
                            'monthDay'      => $monthDay,
                            'url'           => $this->getUrlDay($dateDict[$date]->date),
                            'isUploadable'  => $canUpload && ($dateIterator <= $dateUploadFrom),
                            'likesCount'    => $dateDict[$date]['likes_count'],
                        ];

                if ($dateDict[$date]->is_deleted) {
                    if ($canUpload) {
                        $drop['isDeletedVislble'] = true;
                    } else {
                        $drop['isDeleted'] = true;
                    }
                }

                // Did user liked it?
                if (!$user->isGuest) $drop['isLiked'] = !empty($likesHash[$dateDict[$date]['id']]);

                $blankSpace = false;
            } else {
                $drop = [
                            'date'          => $date,
                            'monthDay'      => $monthDay,
                            'isUploadable'  => $dateIterator <= $dateUploadFrom,
                            'isEmpty'       => true
                        ];
            }

            $drop['monthTitle'] = $this->monthTitle[$month - 1];
            $drop['blankSpace'] = $blankSpace;
            
            if (!$blankSpace || $canUpload) $this->calendar[] = $drop;

            if ($dateIterator->format('Y-m-d') == $dateTarget->format('Y-m-d')) break;
            date_sub($dateIterator, $step);
        }

        $this->yearStart        = $year;
        $this->yearEnd          = $year + 1;
        $this->isDeleted        = $this->is_deleted ? true : false;
        $this->isHidden         = $this->status != self::statusPublic;
    }

    /**
    * Mark story for deletion
    */
    public function markDeleted() {
        if (!$this->is_deleted) {
            $this->is_deleted = true;
            $this->time_deleted = time();
            $this->save();

            return true;
        } else {
            return false;
        }
    }

    /**
    * Undelete story
    */
    public function undelete() {
        if ($this->is_deleted) {
            $this->is_deleted = false;
            $this->time_deleted = 0;
            $this->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Disposes deleted stories completely
     * @static
     *
     */
    public static function deleteMarked($maxDelete = 100) {
        $lifeTime = Helpers::getParam('story/deletedLifetime');
        if (!$lifeTime) throw new \Exception('No lifetime parameter for story deletion');

        $storyList = self::find()->where(self::makeCondition([
                                                                'is_deleted'    => 1,
                                                                'time_deleted'  => ['<', time() - $lifeTime]
                                                            ]))->limit($maxDelete)->all();

        $mo = Media::getMediaOptions('storyImage');

            foreach($storyList as $story) {
                Helpers::transact(function() use($story, $mo) {
                    $images = $story->hasMany(Media::className(), ['target_id' => 'id', 'target_type' => 'type'])
                    ->where(['type' => $mo[Media::mediaTypeId], 'is_deleted' => 0])
                    ->all();

                    // Drop images
                    if ($images) {
                        foreach ($images as $image) {
                            $image->markDeleted();
                        }
                    }

                    // Drop comments
                    $comments = $story->comments;
                    if ($comments) {
                        foreach ($comments as $comment) {
                            $comment->delete();
                        }
                    }

                    $story->delete();
                });
            }
    }

    /**
    * Handle the story comment stuff
    */
    public function afterComment($data) {
        if (!empty($data['isNewComment'])) {
            // Notify story owner
            if ($data['target']->created_by != $data['comment']->created_by) {
                MQueue::compose()
                                ->toUser($data['target']->created_by, ['checkOption' => 'notify'])
                                ->subject('Новый комментарий к вашей истории')
                                ->bodyTemplate('comment.php', [
                                                                        'target'                => $data['target'],
                                                                        'comment'               => $data['comment'],
                                                                        'commentAuthor'         => $data['comment']->author,
                                                                        'username'              => \app\components\HelpersName::parseName($data['target']->author->fullname, 'уважаемый Пользователь')
                                                                    ])
                                ->send();
            }

            // Notify parent comment owner
            if (!empty($data['parentComment']) && $data['comment']->created_by != $data['parentComment']->created_by && $data['parentComment']->created_by != $data['target']->created_by) {
                MQueue::compose()
                                ->toUser($data['parentComment']->created_by, ['checkOption' => 'notify'])
                                ->subject('Ответ на ваш комментарий')
                                ->bodyTemplate('commentReply.php', [
                                                                        'target'                => $data['target'],
                                                                        'comment'               => $data['comment'],
                                                                        'commentAuthor'         => $data['comment']->author,
                                                                        'parentComment'         => $data['parentComment'],
                                                                        'parentCommentAuthor'   => $data['parentComment']->author,
                                                                        'username'              => \app\components\HelpersName::parseName($data['parentComment']->author->fullname, 'уважаемый Пользователь')
                                                                    ])
                                ->send();
            }
        }
    }

    /**
     * After media count had changed
     */
    public function afterMediaCountChanged() {
        $isComplete = $this->media_count >= 365 ? true : false;
        if ($this->is_complete != $isComplete) {
            $this->is_complete = $isComplete;
            $this->save();
        }
    }

    /**
     * @param $userId
     * @param $date
     * @param $extra
     * @return mixed
     */
    public static function getNotifyStories($userId, $date, $extra = null) {
        if (empty($extra['maxItems'])) $maxItems = 3; else $maxItems = $extra['maxItems'];

        $result = [];
        $conditions = [
                            'created_by' => $userId,
                            'is_deleted' => 0,
                            'time_start' => ['>', time() - 86400 * 365]
                        ];

        $stories = self::find()->where(self::makeCondition($conditions))->all();

        foreach($stories as $story) {
            $lastMedia = $story->fetchImages(['imageLimit' => 1, 'returnOnly' => true]);

            if (!$lastMedia || $lastMedia[0]->date != $date) {
                $story->setScenario('notify');
                $result[] = $story;
                if (count($result) == $maxItems) break;
            }
        }

        return $result;
    }
}
