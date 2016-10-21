<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\modules\api\models\ApiLoginForm;
use app\modules\api\components\ApiController;
use app\modules\api\models\ApiAuthToken;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AuthController extends ApiController {
    const ERR_BAD_TOKEN = 'AUTH_BAD_TOKEN';
    
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
                                'actions' => ['login', 'check-token', 'reuse-token'],
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
                            'login'             => ['post'],
                            'login-by-token'    => ['post'],
                            'logout'            => ['post']
                        ],
                    ];

        return $b;
    }

    /**
    * Returns the array with the data needed for Swagger UI
    */
    public static function getSwaggerData() {
        return [
            'title'                         => 'Auth',
            'description'                   => 'Users authentication is done using this method',
            'methods'                       => [
                '/auth/login'               => [
                    'title' => 'Authenticates Users',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'username',     't' => 'Username', 'f' => 'string'],
                                                    ['n' => 'password',     't' => 'User Password', 'f' => 'string']
                                            ],
                    'responses'             => ['200' => ['s' => 'Token']]
                ],

                '/auth/reuse-token'      => [
                    'title' => 'Checks if session specified by token exists',
                    'method' => 'POST',
                    'params'                => [
                        ['n' => 'accessToken',  't' => 'Access token', 'f' => 'string'],
                    ],
                    'responses'             => ['200' => ['s' => 'Token']]
                ],

                '/auth/logout'               => [
                    'title' => 'Forgets the Web Interface User',
                    'method' => 'POST',
                    'params'                => [],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/auth/check-token'         => [
                    'title' => 'Checks Token Status',
                    'method' => 'GET',
                    'auth' => true,
                    'params'                => [],
                    'responses'             => ['200' => ['s' => 'User']]
                ],
            ]
        ];


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

        if (!$model->hasErrors() && Yii::$app->request->isAjax) {
            $referrer = Yii::$app->request->getReferrer();
            $parsed = parse_url($referrer);

            if (Helpers::isLocalUrl($referrer) && (empty($parsed['path']) || $parsed['path'] != '/site/login')) {
                $redirectUrl = $referrer;
            } else {
                $redirectUrl = $model->user->url;
            }

            $this->addContent($redirectUrl, 'redirect');
        }
    }

    public function actionCheckToken($accessToken) {
        $this->addContent(ApiAuthToken::getToken($accessToken, ['noTouch' => true]));
    }


    public function actionReuseToken($accessToken) {
        $token = ApiAuthToken::getToken($accessToken, ['noTouch' => true]);

        if (!$token) {
            throw new \app\components\ControllerException('Bad token');
        } else {
            $this->addContent($token->user);
        }
    }

    /**
     * Log out
     *
     * @param string $username
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        if (Yii::$app->request->isAjax) $this->addContent(\yii\helpers\Url::base(true), 'redirect');
    }
}
