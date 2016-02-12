<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Story;
use app\models\Mosaic;
use app\models\RegisterForm;
use app\components\MyController;
use app\components\Captcha;
use app\components\Ml;

class SiteController extends MyController
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'contact'],
                'rules' => [
                    [
                        'actions'   => ['secret'],
                        'allow'     => true,
                        'roles'     => ['admin']
                    ],

                    [
                        'actions'   => ['index', 'login', 'captcha', 'help', 'howto', 'error', 'cave'],
                        'allow'     => true,
                        'roles'     => ['?', '@']
                    ],

                    [
                        'allow'     => false,
                        'roles'     => ['@']
                    ]
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'], 'login' => ['post']
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionCave() {
        $list = Story::find()->all();

        foreach ($list as $item) {
            echo '<a href="' . $item->url . '">' . $item->url . '</a><br>';
        }
    }

    public function actionIndex() {
        $user = Yii::$app->user;
        if (!$user->isGuest) $this->redirect($user->identity->url);

        $mItem = Mosaic::getCurrent();

        if ($mItem) {
            $data = $mItem->parsedData;

            $this->addJsVars([
                                'ids'               => $data['ids'],
                                'urls'              => $data['urls'],
                                'currentMosaicId'   => $mItem->id,
                                'maxSprites'        => Mosaic::thumbLimit,
                                'maxSpritesPerFile' => Mosaic::fileThumbLimit
                            ]);
        }  

        $model = new RegisterForm();
        $this->layout = 'front';

        return $this->render('index', ['model' => $model]);
    }

    public function actionLogin() {        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionConfirm() {

    }

    public function actionRecover() {

    }

    public function actionRecoverConfirm() {

    }    

    public function actionHelp() {
         $this->setTitle(Ml::t('About the project'));
        return $this->render('help');
    }

    public function actionHowto() {
        $this->setTitle(Ml::t('Howto'));
        return $this->render('howto');
    }

    public function actionCaptcha() {
        $captcha = new Captcha(6, 0, 5);
        
        @session_start();
        $_SESSION['CAPTCHAString'] = $captcha->getCaptchaString();
        $captcha->makeCaptcha();    
    }
}
