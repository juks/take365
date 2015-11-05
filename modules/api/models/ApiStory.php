<?php

namespace app\modules\api\models;

use app\models\Story as BaseStory;

class ApiStory extends BaseStory {

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'id'    => 'id',
            'title' => 'title',
        ];
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