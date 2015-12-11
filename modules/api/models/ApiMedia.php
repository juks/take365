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
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }
}

?>