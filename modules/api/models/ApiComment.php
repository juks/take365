<?php

namespace app\modules\api\models;

use app\models\Comment;
use app\components\Helpers;

class ApiComment extends Comment {
    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }
}

?>