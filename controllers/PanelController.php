<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Story;
use app\models\Mosaic;
use app\models\RegisterForm;
use app\models\Blog;
use app\components\MyController;
use app\components\Captcha;
use app\components\Ml;

class PanelController extends MyController
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'contact'],
                'rules' => [
                    [
                        'actions'   => ['write'],
                        'allow'     => true,
                        'roles'     => ['?', 'admin']
                    ],

                    [
                        'allow'     => false,
                        'roles'     => ['@']
                    ]
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'oAuthSuccess'],
            ],
        ];
    }

    public function actionWrite() {
        return $this->render('write', []);
    }

}
