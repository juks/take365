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
    public $yearStart;
    public $yearEnd;
    public $isDeleted;
    public $isHidden;
    public $images;
    public $imagesCount;
    public $progress;

    public $monthTitle = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    public $monthTitleGen = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];

    protected $_monthQuota = 5;
    protected $_authorCache = false;

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

        if (!$this->_oldAttributes['description'] !== $this->description) $this->description_jvx = HelpersTxt::simpleText($this->description);

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
        if ($this->_authorCache === false)
            $this->_authorCache = User::find()->where(User::getActiveCondition())->andWhere(['id' => $this->created_by])->one();

        return $this->_authorCache ? $this->_authorCache->url . '/story/' . $this->id : null;
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

        $author = User::find()->where(User::getActiveCondition())->andWhere(['id' => $this->created_by])->one();
        if ($author) $a[] = ['username' => $author->username, 'url' => $author->url];

        return $a;
    }

    /**
    * Returns story progress information
    */
    public function getProgress() {
        $totalDays      = 365;
        $imagesCount    = $this->imagesCount;
        $lastTime       = $imagesCount ? strtotime($this->images[0]['date']) : $this->time_start;
        $delayDays      = intval((time() - $lastTime) / 86400);
        $passedDays     = intval((time() - $this->time_start) / 86400);
        $percentsComplete = sprintf('%2.1f', (($imagesCount / $totalDays) * 100));
        if ($percentsComplete == 100) $percentsComplete = 100;

        $this->progress = [
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

        if ($delayDays <= 365 && !$percentsComplete != 100) $this->progress['delayDays'] = $delayDays;
    }

    /**
     * Images relation
     */
    public function getImages($extra = []) {
        $mo = Media::getMediaOptions('storyImage');
        $limit = !empty($extra['imageLimit']) ? $extra['imageLimit'] : null;
        $this->images = $this->hasMany(Media::className(), ['target_id' => 'id', 'target_type' => 'type'])->where(['type' => $mo[Media::typeId], 'is_deleted' => 0])->orderBy('date DESC')->limit($limit)->all();
    }

    public function getImagesCount($extra = []) {
        $mo = Media::getMediaOptions('storyImage');
        $this->imagesCount = $this->hasMany(Media::className(), ['target_id' => 'id', 'target_type' => 'type'])->where(['type' => $mo[Media::typeId], 'is_deleted' => 0])->orderBy('date DESC')->count();
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
        $this->getImages($extra);
        $this->getImagesCount($extra);
        $this->getProgress();
        $this->isDeleted = $this->is_deleted;
        $this->isHidden  = $this->status != self::statusPublic;
    }

    /**
    * Formats story data for user page display
    */
    public function format($extra = []) {
        $this->calendar = [];
        $this->getImages($extra);
        $this->getImagesCount($extra);
        $this->getProgress();

        $lastMonth = null;
        $canManage = $this->hasPermission(Yii::$app->user, IPermissions::permWrite);
        $dateDict = [];

        foreach ($this->images as $image) $dateDict[$image['date']] = $image;

        $dt = new \DateTime('@' . $this->time_start);
        $now = new \DateTime('@' . time());
        $diff = $now->diff($dt);
        $daysDiff = $diff->days;
        if ($diff->h || $diff->i || $diff->s) $daysDiff ++;

        if ($daysDiff > 365) $daysDiff = 365;

        $dt->add(new \DateInterval('P' . $daysDiff . 'D'));

        $dateStep = new \DateInterval('P1D');
        $blankSpace = true;

        for ($i = 0; $i < $daysDiff; $i++) {
            $date       = $dt->format('Y-m-d');

            $p          = preg_split('/-/', $date);
            $year       = intval($p[0]);
            $month      = intval($p[1]);
            $monthDay   = intval($p[2]);

            if (!empty($dateDict[$date])) {
                $drop = [
                            'date'          => $date,
                            'image'         => ['url' => $dateDict[$date]['t']['squareCrop']['200']['url'], 'width' => 200, 'height' => 200],
                            'imageLarge'    => ['url' => $dateDict[$date]['t']['squareCrop']['400']['url'], 'width' => 400, 'height' => 400],
                            'monthDay'      => $monthDay,
                            'url'           => $this->getUrlDay($dateDict[$date]->date)
                        ];

                if ($dateDict[$date]->is_deleted) {
                    if ($canManage) {
                        $drop['isDeletedVislble'] = true;
                    } else {
                        $drop['isDeleted'] = true;
                    }
                }

                $blankSpace = false;
            } else {
                $drop = [
                            'date'          => $date,
                            'monthDay'      => $monthDay,
                            'isEmpty'       => true
                        ];
            }

            if (!$lastMonth || $lastMonth != $month) $drop['monthSwitch'] = $this->monthTitle[$month - 1];
            $drop['blankSpace'] = $blankSpace;

            $lastMonth          = $month;
            if (!$blankSpace || $canManage) $this->calendar[] = $drop;

            $dt->sub($dateStep);
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
            $this->save();

            return true;
        } else {
            return false;
        }
    }
}
