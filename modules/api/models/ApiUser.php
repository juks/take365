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
            'default' => ['username', 'fullname', 'password', 'email', 'description', 'sex', 'timezone']
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