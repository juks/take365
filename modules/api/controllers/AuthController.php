<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\models\AuthToken;
use app\modules\api\models\ApiLoginForm;
use app\modules\api\components\ApiController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AuthController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'actions' => ['logout'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],

                            [
                                'actions' => ['login', 'check-token'],
                                'allow' => true,
                                'roles' => ['?', '@'],
                            ],

                            [
                                'allow' => false,
                                'roles' => ['@']
                            ]
                        ],
            ];

        $b['verbs'] = [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'funkout' => ['post'],
                        ],
                    ];

        return $b;
    }

    protected function getModelClass() {
        throw new Exception("Method getModelClass() is not supported by this controller");
    }

    /**
     * Log in
     *
     * @param string $username
     */
    public function actionLogin($username, $password) {
        $model = new ApiLoginForm();

        if ($model->load(Helpers::getRequestParams('post'))) $model->login();
       
        $this->addContent($model);
    }

    public function actionCheckToken($accessToken) {
        $this->addContent(AuthToken::getToken($accessToken, ['noTouch' => true]));
    }

    /**
     * Log out
     *
     * @param string $username
     */
    public function actionLogout() {
        Yii::$app->user->logout();
    }
}
