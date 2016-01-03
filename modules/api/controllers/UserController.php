<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\modules\api\components\ApiController;
use app\modules\api\models\ApiUser;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class UserController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['get', 'list', 'update-profile'],
                    'allow' => true,
                    'roles' => ['@'],
                ],

                [
                    'actions' => ['check-username', 'check-email', 'register'],
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
                'update-profile' => ['post'],
                'register'		 => ['post']
            ],
        ];

        return $b;
    }

    protected function getModelClass() {
        return ApiUser::className();
    }

    /**
     * Fetches user profile data
     *
     * @param int $id
     */
    public function actionGet($id = null, $username = null) {
        if ($id) {
        	$this->addContent($this->checkModelPermission(intval($id), IPermissions::permRead));
        } else {
			$this->addContent($this->checkModelPermission($username, IPermissions::permRead));
        }
    }

    /**
     * Lists users
     *
     * @param int $page
     * @param int $maxItems
     */
    public function actionList($page = 1, $maxItems = 10) {
    	if ($maxItems > 500) $maxItems = 500;

        $this->addContent(ApiUser::find()->where(['is_active' => 1])->orderBy('time_registered')->offset(($page - 1) * $maxItems)->limit($maxItems)->all());
    }

	/**
	 * Проверить доступность имени пользователя
	 *
	 * @param string $username
	 */
	public function actionCheckUsername($username) {
		$user = new ApiUser();
		$user->checkField('username', $username);
		if ($user->hasErrors()) $this->addContent($user);
	}

	/**
	 * Проверить доступность имейла
	 *
	 * @param string $message
	 */
	public function actionCheckEmail($email) {
		$user = new ApiUser();
		$user->checkField('email', $email);
		if ($user->hasErrors()) $this->addContent($user);
	}

	/**
	 * Регистрация пользователя
	 *
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 */
	public function actionRegister($username, $email, $password) {
		$user = new ApiUser();

		$user->load(Helpers::getRequestParams('post'));
		$user->save();

		$this->addContent($user);
	}

	/**
	 * Обновление профиля пользователя
	 *
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 */
	public function actionUpdateProfile($id) {
		$model = $this->checkModelPermission($id, IPermissions::permWrite);
		$model->load(Helpers::getRequestParams('post'));
		$model->save();

		$this->addContent($model);
	}
}
