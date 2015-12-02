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
        $f =  [
            'id'        => 'id',
            'status'    => 'status',
            'title'     => 'title',
            'url'       => function() { return $this->url; },
            'authors'   => function() { return $this->authors; },
            'progress'  => function() { return $this->progress; },
        ];

        if ($this->scenario == 'default') $f['images'] = function() { return $this->images; };

        return $f;
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
}

?>