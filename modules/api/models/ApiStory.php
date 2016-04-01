<?php

namespace app\modules\api\models;

use app\models\Story;
use app\components\Helpers;
use app\models\mediaExtra\MediaCore;
use app\modules\api\models\ApiMedia;

class ApiStory extends Story {
    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }
}

?>