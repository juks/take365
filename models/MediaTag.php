<?php

namespace app\models;

use app\models\base\MediaTagBase;

use Yii;
use app\components\Helpers;
use app\components\traits\TModelExtra;

/**
 * Tags for media
 */
class MediaTag extends MediaTagBase {
    use TModelExtra;

    /**
     *   Sets the lists of fields that are available for public exposure
     **/
    public function fields() {
        return [
                    'name'        => 'name',
        ];
    }

    /**
     *   Sets the Like model scenarios
     **/
    public function scenarios() {
        return [
            'default' => ['name']
        ];
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
        } else {
            $this->time_updated = time();
        }

        return parent::beforeValidate();
    }
}