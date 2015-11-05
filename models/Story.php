<?php

namespace app\models;

use app\models\base\StoryBase;
use Yii;
use app\components\traits\TCheckField;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;
use app\components\interfaces\IPermissions;
use app\components\interfaces\IGetType;

/**
 * Story class
 */
class Story extends StoryBase implements IPermissions, IGetType {
    use TCheckField;
    use THasPermission;
    use TModelExtra;

    protected $_monthQuota = 5;

	public function getIsPublic() {
        return true;
    }

    public function getCreatorIdField() {
        return 'created_by';
    }

    public function getType() {
        return 2;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
    	$user = Yii::$app->user;

        if ($this->isNewRecord) {
            $this->time_created = time();
            if (!$this->created_by) $this->created_by = $user->id;
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
}