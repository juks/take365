<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\models\MQueue;
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
                    'actions' => ['get', 'list', 'update-profile', 'foo'],
                    'allow' => true,
                    'roles' => ['@'],
                ],

                [
                    'actions' => ['check-username', 'check-email', 'register', 'recover', 'recover-update'],
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
                //'register'		 => ['post']
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
	 * Check if username is available
	 *
	 * @param string $username
	 */
	public function actionCheckUsername($username) {
		$user = new ApiUser();
		$user->checkField('username', $username);
		if ($user->hasErrors()) $this->addContent($user);
	}

	/**
	 * Check if email is available
	 *
	 * @param string $message
	 */
	public function actionCheckEmail($email) {
		$user = new ApiUser();
		$user->checkField('email', $email);
		if ($user->hasErrors()) $this->addContent($user);
	}

	/**
	 * Register new user
	 *
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 */
	public function actionRegister($username, $email, $password) {
        $form = new \app\modules\api\models\ApiRegisterForm;
        
        if ($form->load(Helpers::getRequestParams('post'))) {
            $user = new ApiUser();
            $user->load($form->attributes);

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {   
                if ($user->save()) {
                    $user->afterRegister();
                }
            } catch (\Exception $e) {
                $transaction->rollback();

                throw $e;
            }

            $transaction->commit();

            $this->addContent($user);
        } else {
            $this->addContent($form);
        }
	}

    /**
     * Request password recovery
     *
     * @param string $email
     */
    public function actionRecover($email) {
        $form = new \app\modules\api\models\ApiRecoverForm();
        
        if ($form->load(Helpers::getRequestParams('post')) && $form->validate()) {
            $user = ApiUser::getActiveUser($email);
            if ($user) {
                $user->recover();
                $this->addContent($user);
            };
        } else {
            $this->addContent($form);
        }
    }

    /**
     * Update user password on successful recovery
     *
     * @param id $id
     * @param string $code
     * @param string $password
     */
    public function actionRecoverUpdate($id, $code, $password) {
        $form = new \app\modules\api\models\ApiRecoverUpdateForm();
        
        if ($form->load(Helpers::getRequestParams('post')) && $form->validate()) {
            $user = ApiUser::findByPasswordResetToken($code, $id);
            if ($user) {
                $user->recoverUpdate($password);

                $this->addContent($user);
            };
        } else {
            $this->addContent($form);
        }
    }

	/**
	 * Update user profile
	 *
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 */
	public function actionUpdateProfile($id) {
		$model = $this->checkModelPermission(intval($id), IPermissions::permWrite);
		$model->load(Helpers::getRequestParams('post'));
		$model->save();

		$this->addContent($model);
	}
}
