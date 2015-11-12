<?php

namespace app\modules\api\components;

use Yii;
use app\components\MyJsonController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class ApiController extends MyJsonController {
    public function behaviors() {
        $b = parent::behaviors();

        if (Yii::$app->user->isGuest) {
            $b['authenticator'] = [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
                'except' => ['index', 'login', 'error', 'check-username', 'check-email', 'register']
            ];
        }

        return $b;
    }

    /**
     * Before action
     *
     * @param string $action
     */
    public function init() {
        parent::init();

        Yii::$app->user->loginUrl = null;
        Yii::$app->errorHandler->errorAction = 'api/default/error';
    }

    public function actionError() {

    }
}
