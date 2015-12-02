<?php

namespace app\models;

use Yii;
use app\models\base\StoryBase;
use app\components\traits\TCheckField;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;
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

    protected $_monthQuota = 5;

    /**
    *   Sets the Story model scenarios
    **/    
    public function scenarios() {
        return [
            'import' => ['id', 'created_by', 'status', 'is_deleted', 'time_deleted', 'is_active', 'time_created', 'time_updated', 'time_start', 'time_published', 'media_count', 'title', 'description']
        ];
    }

    /**
     * Returns public criteria
     */
	public function getIsPublic() {
        return $this->status == 0;
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
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            $this->time_created = time();
            $this->time_start = time();
            if ($this->scenario != 'import' && !$this->created_by) $this->created_by = Yii::$app->user->id;
        } else {
            $this->time_updated = time();
        }

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
}