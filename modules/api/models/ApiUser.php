<?php

namespace app\modules\api\models;

use app\models\User as BaseUser;

class ApiUser extends BaseUser {
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
            'id'       => 'id',
            'username' => 'username',
            //'test' => function ($model) { return 'best' }
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