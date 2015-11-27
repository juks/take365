<?php

namespace app\modules\api\components;

use Yii;
use app\components\MyJsonController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;

class ApiController extends MyJsonController {
    public function behaviors() {
        $b = parent::behaviors();

        if (Yii::$app->user->isGuest) {
            $b['authenticator'] = [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    \app\modules\api\components\ApiHttpBearerAuth::className(),
                    \app\modules\api\components\ApiQueryParamAuth::className(),
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

    /**
     * runAction override
     *
     * @param string $id
     * @param array params
     */
    public function runAction($id, $params = []) {
         // Extract the params from the request and bind them to params
         $params = \yii\helpers\BaseArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);

         return parent::runAction($id, $params);
    }
}
