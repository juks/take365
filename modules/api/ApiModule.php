<?php

namespace app\modules\api;

class ApiModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\api\controllers';

    public function init() {
        parent::init();

    	//\Yii::$app->user->enableSession = false;
        // custom initialization code goes here
    }
}
