<?php

namespace app\modules\api\models;

use app\models\User;
use app\models\Media;

class ApiUser extends User {
    /**
    *   Sets the API scenarios
    **/    
    public function scenarios() {
        return [
            'default' => ['username', 'password', 'email', 'description']
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'id'            => 'id',
            'username'      => 'username',
            'userpic'       => function() { $up = $this->userpic; return $up ? $up->getThumbData(Media::resizeMaxSide, 100) : null; },
            'userpicLarge'  => function() { $up = $this->userpic; return $up ? $up->getThumbData(Media::resizeMaxSide, 200) : null; }
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