<?php

namespace app\modules\api\components;

/**
 * QueryParamAuth for iOS and Android variable
 */

class ApiQueryParamAuth extends \yii\filters\auth\QueryParamAuth
{
    public $tokenParam = 'accessToken';
}