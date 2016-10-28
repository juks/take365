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

    protected $_subjects = [
        'food' => 'еда',
        'cat' => 'кошка',
        'dog' => 'собака',
        'car' => 'машина',
        'brick' => 'кирпичи',
        'horse' => 'лошадь',
        'bicycle' => 'велосипед',
        'flower' => 'цветок',
        'pattern' => 'узор',
        'selfie' => 'автопортрет',
        'ship' => 'корабль',
        'painting' => 'картина',
        'bird' => 'птица',
        'road' => 'дорога',
        'bw' => 'чёрно-белое',
        'snow' => 'снег',
        'darkness' => 'темонта'
    ];

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

    /**
     * Retrieve images by a random criteria
     * @return array
     */
    public function listByRandomTag() {
        $tag = array_keys($this->_subjects)[rand(0,count($this->_subjects) - 1)];

        return ['tag' => ucfirst($this->_subjects[$tag]), 'list' => \app\models\MediaTagLink::listByTag($tag)];
    }
}