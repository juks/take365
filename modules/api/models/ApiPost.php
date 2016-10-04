<?php

namespace app\modules\api\models;

use app\models\Post;

class ApiPost extends Post {
    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire object root is okay
    **/
    public function formName() {
        return '';
    }
}

?>