<?php

namespace app\modules\api\models;

use app\models\Media as BaseMedia;
use app\models\Story;

class ApiMedia extends BaseMedia {
        /**
    *   Sets the API scenarios
    **/    
    public function scenarios() {
        return [
            'default' => ['date']
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        $fields = [
                        'id'            => 'id',
                        'title'         => 'title',
                        'thumb'         => function() { return $this->getThumbData(BaseMedia::resizeMaxSide, $this->getOption('mainThumbDimension')); },
                        'thumbLarge'    => function() { return $this->getThumbData(BaseMedia::resizeMaxSide, $this->getOption('largeThumbDimension')); }
                    ];

        if ($this->target_type == Story::typeId) $fields['date'] = 'date';

        return $fields;
    }

    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }
}

?>