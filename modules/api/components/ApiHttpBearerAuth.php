<?php

namespace app\modules\api\components;

/**
 * QueryParamAuth for iOS and Android variable
 */

class ApiHttpBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
    public $tokenParam = 'accessToken';
}