<?php

namespace app\models;

use app\models\base\LikeBase;

use Yii;
use app\components\Helpers;
use app\components\traits\TModelExtra;

/**
 * Like class
 */
class Like extends LikeBase {
	use TModelExtra;

    /**
     *   Sets the Like model scenarios
     **/
    public function scenarios() {
        return [
            'default' => ['target_id', 'target_type', 'is_active']
        ];
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
            if (!$this->created_by) $this->created_by = Yii::$app->user->id;
        }

        return parent::beforeValidate();
    }

}