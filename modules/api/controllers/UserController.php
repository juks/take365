<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\modules\api\components\ApiController;
use app\modules\api\models\ApiUser;

class UserController extends ApiController {
    protected function getModelClass() {
        return ApiUser::className();
    }

	/**
	 * Проверить доступность имени пользователя
	 *
	 * @param string $username
	 */
	public function actionCheckUsername($username) {
		$user = new ApiUser();
		$user->checkField('username', $username);
		$this->addContent(!$user->hasErrors());
	}

	/**
	 * Проверить доступность имейла
	 *
	 * @param string $message
	 */
	public function actionCheckEmail($email) {
		$user = new ApiUser();
		$user->checkField('email', $email);
		$this->addContent(!$user->hasErrors());
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
		$model = $this->checkModelPermission($id, 'write');
		$model->load(Helpers::getRequestParams('post'));
		$model->save();

		$this->addContent($model);
	}
}
