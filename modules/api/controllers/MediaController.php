<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
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
                                'actions' => ['upload'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],

                            [
                                'allow' => false,
                                'roles' => ['@']
                            ]
                        ],
            ];

        return $b;
    }

    protected function getModelClass() {
        return ApiMedias::className();
    }

    /**
     * Log in
     *
     * @param string $username
     */
    public function actionUpload($username, $password) {
        $model = new ApiLoginForm();

        if ($model->load(Helpers::getRequestParams('post'))) $model->login();
       
        $this->addContent($model);
    }
}
