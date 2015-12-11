<?php

namespace app\models;

use Yii;
use app\models\base\StoryBase;
use app\components\Helpers;
use app\components\HelpersTxt;
use app\components\traits\TCheckField;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;
use app\models\Media;
use app\models\mediaExtra\MediaCore;
use app\models\mediaExtra\TMediaUploadExtra;
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

    const typeId = 2;

    const statusPublic = 0;

    public $calendar;

    protected $_monthQuota = 5;
    protected $_mediaCache;
    protected $_progressCache;

    /**
    *   Sets the Story model scenarios
    **/    
    public function scenarios() {
        return [
            'import' => ['id_old', 'created_by', 'status', 'is_deleted', 'time_deleted', 'is_active', 'time_created', 'time_updated', 'time_start', 'time_published', 'media_count', 'title', 'description', 'description_jvx']
        ];
    }

    public static function getActiveCondition() {
        return ['status' => self::statusPublic];
    }

    /**
     * Returns public criteria
     */
	public function getIsPublic() {
        return $this->status == self::statusPublic;
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
    **/
    public static function getActiveStory($storyId) {
        $story = self::findOne($storyId);
        $user = Yii::$app->user;

        if ($story) {
            if ($story->status == self::statusPublic) {
                return $story;
            } elseif ($story->created_by == $user->id) {
                return $story;
            }
        }

        return null;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
            if (!$this->time_start) $this->time_start = time();
            if ($this->scenario != 'import' && !$this->created_by) $this->created_by = Yii::$app->user->id;
        } else {
            $this->time_updated = time();
        }

        // if (!$this->_oldAttributes['description'] !== $this->description) $this->description_jvx = HelpersTxt::simpleText($this->description);

        return parent::beforeValidate();
    }

    /**
     * Checks user story quota
     *
     * @param  object $user
     * @return string $type
     */
    public function checkQuota($user = null, $type = 'month') {
        if (!$user) $user = Yii::$app->user;

        $cnt = $this->getCount(['created_by' => $user->id, 'time_created' => ['>=', time() - 86400]]);

        return $cnt <= $this->_monthQuota;
    }

    /**
    * Checks if the given date is valid for this story (fits within one year span)
    * @param  string $date
    */
    public function isValidDate($date) {
        $dtDate = new \DateTime($date);
        $dtStart = new \DateTime('@' . $this->time_start);
        $interval = date_diff($dtStart, $dtDate);

        if (!$interval->invert && $interval->days >= 0 && $interval->days <= 365) return true; else return false;
    }

    /**
    * Forms story URL
    */
    public function getUrl() {
        $author = User::find()->where(User::getActiveCondition())->andWhere(['id' => $this->created_by])->one();

        return $author ? $author->url . '/story/' . $this->id : null;
    }

    /**
    * Returns authors
    */
    public function getAuthors() {
        $a = [];

        $author = User::find()->where(User::getActiveCondition())->andWhere(['id' => $this->created_by])->one();
        if ($author) $a[] = ['username' => $author->username, 'url' => $author->url];

        return $a;
    }

    /**
    * Returns story progress information
    */
    public function getProgress() {
        if (!$this->_progressCache) {
            $totalDays      = 365;
            $images         = $this->images;
            $imagesCount    = count($images);
            $lastTime       = $imagesCount ? strtotime($images[0]['date']) : $this->time_start;
            $delayDays      = intval((time() - $lastTime) / 86400);
            $passedDays     = intval((time() - $this->time_start) / 86400);
            $percentsComplete = sprintf('%2.1f', (($imagesCount / $totalDays) * 100));
            if ($percentsComplete == 100) $percentsComplete = 100;

            $this->_progressCache = [
                        'percentsComplete'      => $percentsComplete,
                        'isComplete'            => $percentsComplete == 100,
                        'passedDays'            => $passedDays,
                        'totalImages'           => $imagesCount,
                        'totalImagesTitle'      => Helpers::countCase($imagesCount, 'изображений', 'изображения', 'изображание'),
                        'totalDays'             => $totalDays,
                        'delayDays'             => $delayDays,
                        'delayDaysTitle'        => Helpers::countCase($delayDays, 'дней', 'дня', 'день'),
                        'delayDaysMakeSense'    => $delayDays > 3 && $delayDays <= 365,
                    ];

            if ($delayDays <= 365 && !$percentsComplete != 100) $this->_progressCache['delayDays'] = $delayDays;
        }

        return $this->_progressCache;
    }

    /**
     * Images relation
     */
    public function getImages() {
        if ($this->_mediaCache) {
            return $this->_mediaCache;
        } else {
            $mo = Media::getMediaOptions('storyImage');
            $this->_mediaCache = $this->hasMany(Media::className(), ['target_id' => 'id', 'target_type' => 'type'])->where(['type' => $mo[Media::typeId], 'is_deleted' => 0])->orderBy('date DESC');

            return $this->_mediaCache;
        }
    }

    /**
    * Returns story title if any or default value
    */
    public function getTitleFilled() {
        return $this->title ? $this->title : 'Без названия';
    }

    /**
    * Formats story data for user page display
    */
    public function format() {
        $this->calendar = [];
        $images = $this->images;
        $lastMonth = null;
        $canManage = $this->hasPermission(Yii::$app->user, IPermissions::permWrite);

        foreach ($images as $image) {
            $month = preg_split('/-/', $image->date)[1];
            $monthDay = preg_split('/-/', $image->date)[2];

            $drop = [
                        'date'          => $image->date,
                        'image'         => ['url' => $image['t']['squareCrop']['200']['url'], 'width' => 200, 'height' => 200],
                        'imageLarge'    => ['url' => $image['t']['squareCrop']['400']['url'], 'width' => 400, 'height' => 400],
                        'monthDay'      => $monthDay
                    ];

            if (!$lastMonth || $lastMonth != $month) $drop['monthSwitch'] = true;
            if ($image->is_deleted) $drop['isDeleted'] = true;
            if ($image->is_deleted) {
                if ($canManage) $drop['isDeletedVislble']; else $drop['isInvisible'];
            } 

            $lastMonth = preg_split('/-/', $image->date)[1];
            $this->calendar[] = $drop;
        }
    }
}