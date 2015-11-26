<?php

namespace app\modules\api\models;

use app\models\Story;
use app\components\Helpers;
use app\models\mediaExtra\MediaCore;
use app\modules\api\models\ApiMedia;

class ApiStory extends Story {
    protected $_mediaCache;

    /**
    *   Sets the API scenarios
    **/    
    public function scenarios() {
        return [
            'default' => ['status', 'title', 'description']
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'id'        => 'id',
            'status'    => 'status',
            'title'     => 'title',
            'url'       => function() { return $this->url; },
            'progress'  => function() { return $this->progress; },
            'images'    => function() { return $this->images; }
        ];
    }

    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }

    /**
     * Images relation
     */
    public function getImages() {
        if ($this->_mediaCache) {
            return $this->_mediaCache;
        } else {
            $mo = ApiMedia::getMediaOptions('storyImage');
            $this->_mediaCache = $this->hasMany(ApiMedia::className(), ['target_id' => 'id', 'target_type' => 'type'])->where(['type' => $mo[ApiMedia::typeId], 'is_deleted' => 0])->orderBy('date DESC');

            return $this->_mediaCache;
        }
    }

    public function getProgress() {
        $daysTotal      = 365;
        $images         = $this->images;
        $imagesCount    = count($images);
        $lastTime       = $imagesCount ? strtotime($images[0]['date']) : $this->time_start;
        $delayDays      = intval((time() - $lastTime) / 86400);
        $passedDays     = intval((time() - $this->time_start) / 86400);

        return [
                    'percentsComplete'  => sprintf('%2.1f', (($imagesCount / $daysTotal) * 100)),
                    'delayDays'         => $delayDays,
                    'passedDays'        => $passedDays,
                    'totalImages'       => $imagesCount,
                    'totalImagesTitle'  => Helpers::countCase($imagesCount, 'изображений', 'изображения', 'изображание'),
                ];
    }
}

?>