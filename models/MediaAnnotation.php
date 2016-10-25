<?php

namespace app\models;

use app\models\base\MediaAnnotationBase;

use Yii;
use app\components\Helpers;
use app\components\traits\TModelExtra;

/**
 * Like class
 */
class MediaAnnotation extends MediaAnnotationBase {
    use TModelExtra;

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
            'default' => ['media_id', 'data']
        ];
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if (is_array($this->data) || is_object($this->data)) $this->data = json_encode($this->data);
        if (is_array($this->extra) || is_object($this->extra)) $this->extra = json_encode($this->extra);

        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
        } else {
            $this->time_updated = time();
        }

        return parent::beforeValidate();
    }
}