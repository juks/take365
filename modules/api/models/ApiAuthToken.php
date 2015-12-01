<?php

namespace app\modules\api\models;

use app\models\AuthToken;
use app\components\Helpers;

class ApiAuthToken extends AuthToken {
    public function fields() {
        return [
                    'userId'   => 'user_id',
                    'username' => function() { return $this->username; },
                    'accessed' => 'time_used',
                    'expires'  => 'time_expire'
                ];
    }
}

?>