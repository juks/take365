<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\components\Ml;
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
                    'actions' => ['get', 'list', 'update-profile', 'update-security', 'get-option', 'set-options'],
                    'allow' => true,
                    'roles' => ['@'],
                ],

                [
                    'actions' => ['check-username', 'check-email', 'suggest', 'register', 'recover', 'recover-update'],
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

    /**
    * Returns the array with the data needed for Swagger UI
    */
    public static function getSwaggerData() {
        $user = Yii::$app->user;
        $defaultUserId = !$user->isGuest ? $user->id : null;
        $defaultEmail = isset($user->identity->email) ? $user->identity->email : null;

        return [
                'title'                         => 'Users',
                'description'                   => 'Entire user profile related stuff.',
                'methods'                       => [
                    '/user/profile/{id}'   => [
                        'title' => 'Retrieves User Profile Information',
                        'method' => 'GET',
                        'auth'  => true,
                        'params'                => [['n' => 'id', 't' => 'Username or User Id', 'h' => 'Eg. "bob" for Bob or "1" for user with ID 1', 'f' => 'integer', 'in' => 'path', 'd' => $defaultUserId]],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],

                    '/user/list'   => [
                        'title' => 'Fetches the List of Users',
                        'method' => 'GET',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'page',         't' => 'Page Number',                   'o' => true, 'f' => 'integer'],
                                                        ['n' => 'maxItems' ,    't' => 'Maximal Items Count',           'o' => true, 'f' => 'integer']
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'User']]
                    ],

                    '/user/suggest'   => [
                        'title' => 'Gives Users Suggest for Given Username Part',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Username', 'f' => 'string'],
                                                        ['n' => 'followFlag' ,  't' => 'Include subscription flag for current user', 'o' => true, 'f' => 'boolean'],
                                                        ['n' => 'maxItems' ,    't' => 'Maximal Items Count', 'o' => true, 'f' => 'integer']
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'User']]
                    ],

                    '/user/check-username'   => [
                        'title' => 'Checks If Given Username is Available',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'username', 't' => 'Username', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/check-email'   => [
                        'title' => 'Checks if Given Email is Available',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'email', 't' => 'Preferred Email', 'f' => 'string', 'd' => $defaultEmail],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],


                    '/user/register'   => [
                        'title' => 'Registers New User',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username', 't' => 'Preferred Username', 'f' => 'string'],
                                                        ['n' => 'email', 't' => 'User Email', 'f' => 'string'],
                                                        ['n' => 'password', 't' => 'User Password', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],

                    '/user/recover'   => [
                        'title' => 'Request Password Recovery',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'email', 't' => 'User Email', 'f' => 'string', 'd' => $defaultEmail],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/recover-update'   => [
                        'title' => 'Update User Password Using Recovery Code',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'id',           't' => 'User Id', 'f' => 'integer'],
                                                        ['n' => 'code',         't' => 'Security Code', 'f' => 'string'],
                                                        ['n' => 'password',     't' => 'New Password', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/update-profile'   => [
                        'title' => 'Updates User Profile',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'User Id', 'f' => 'integer', 'd' => $defaultUserId],
                                                        ['n' => 'username',     't' => 'Preferred Username',            'o' => true, 'f' => 'string'],
                                                        ['n' => 'fullname',     't' => 'Preferred Fullname',            'o' => true, 'f' => 'string'],
                                                        ['n' => 'password',     't' => 'User Password',                 'o' => true, 'f' => 'string'],
                                                        ['n' => 'email',        't' => 'User Email',                    'o' => true, 'f' => 'string'],
                                                        ['n' => 'description',  't' => 'User Profile Description',      'o' => true, 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],

                    '/user/update-security'   => [
                        'title' => 'Updates User Secure information',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                            ['n' => 'id',           't' => 'User Id', 'f' => 'integer', 'd' => $defaultUserId],
                            ['n' => 'password',     't' => 'User Password',                 'o' => true, 'f' => 'string'],
                        ],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],

                    '/user/set-options'   => [
                        'title' => 'Set User Options',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'options',      't' => 'Options name-value array', 'f' => 'array'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/get-option'   => [
                        'title' => 'Get User Option Value',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'name',         't' => 'Option name', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],
                ]
             ];
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
     * Suggest users by username
     *
     * @param string $username
     */
    public function actionSuggest($username, $maxItems = 10, $followFlag = false) {
        $this->addContent(ApiUser::suggest([
                                                'username'      => $username,
                                                'followFlag'    => $followFlag
                                            ], $maxItems));
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
        
        if ($form->load(Helpers::getRequestParams('post')) && $form->validate()) {
            $user = new ApiUser();
            $user->is_active = true;
            $user->load($form->attributes);

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {   
                if ($user->save()) {
                    $user->register();
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
            } else {
                $this->addErrorMessage(Ml::t('User not found'), ['field' => 'email']);
            }
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
        $data = Helpers::getRequestParams('post');

		$model->load($data);
		$model->save();

        $model->setOptionValue('notify', !empty($data['optNotify']) ? true : false);

		$this->addContent($model);
    }

    /**
     * Update user security profile
     *
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function actionUpdateSecurity($id) {
        $model = $this->checkModelPermission(intval($id), IPermissions::permWrite);
        $data = Helpers::getRequestParams('post');

        $model->load($data);
        $model->save();

        $this->addContent($model);
    }


    /**
     * Set user options
     *
     * @param array $username
     */
    public function actionSetOptions(array $options) {
        $user = Yii::$app->user;

        $user->identity->setOptionMulti($options);
    }

    public function actionGetOption($name) {
        $user = Yii::$app->user;

        $this->addContent($user->identity->getOptionValue($name));
    }
}
