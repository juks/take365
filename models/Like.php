<?php

namespace app\models;

use app\models\base\LikeBase;

use Yii;
use app\models\User;
use app\components\Helpers;
use app\components\traits\TModelExtra;
use app\components\traits\TAuthor;

/**
 * Like class
 */
class Like extends LikeBase {
	use TModelExtra;
    use TAuthor;

    /**
     *   Sets the lists of fields that are available for public exposure
     **/
    public function fields() {
        return [
                    'author'        => 'author',
                    'timestamp'     => 'time_created'
        ];
    }

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

    /**
     * Returns boolean status for this like
     *
     * @return bool
     */
    public function getValue() {
        return $this->is_active ? true : false;
    }
}