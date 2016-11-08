<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\modules\api\components\ApiController;
use app\modules\api\models\ApiPost;
use app\modules\api\models\ApiUser;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class PostController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['write', 'list'],
                    'allow' => true,
                    'roles' => ['@'],
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
                'write' => ['post'],
            ],
        ];

        return $b;
    }

    /**
    * Returns the array with the data needed for Swagger UI
    */
    public static function getSwaggerData() {
        $user = Yii::$app->user;

        $defaultUserId = null;
        $defaultUsername = null;
        $defaultPostId = 1;

        if (!$user->isGuest) {
            $defaultUserId = $user->id;
            $defaultUsername = !empty($user->identity->username) ? $user->identity->username : null;

        }

        return [
            'title'                     => 'Posts',
            'description'               => 'Provides read and write access to posts data.',
            'methods'                   => [
                '/post/{id}'         => [
                    'title' => 'Fetches Post information',
                    'method' => 'GET',
                    'auth'  => true,
                    'params'                => [['n' => 'id', 't' => 'Post Id', 'f' => 'integer', 'in' => 'path', 'd' => 1]],
                    'responses'             => ['200' => ['s' => 'Post']]
                ],

                '/post/write'      => [
                    'title' => 'Creates or updates post',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                                                    ['n' => 'id',           't' => 'Post Id', 'h'=>'If not given, a new post will be created', 'f' => 'integer', 'd' => $defaultPostId],
                                                    ['n' => 'status',       't' => 'Post Status', 'h'=>'0 — public, 1 — private', 'f' => 'integer', 'e' => [0, 1]],
                                                    ['n' => 'title',        't' => 'Post Title', 'f' => 'string'],
                                                    ['n' => 'description',  't' => 'Post Description', 'f' => 'string'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Post']]
                ],

                '/post/delete-recover'      => [
                    'title' => 'Deletes or recovers post',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                                                    ['n' => 'id',           't' => 'Post Id',             'h'=>'If not given, a new post will be created', 'f' => 'integer', 'd' => $defaultPostId],
                                                    ['n' => 'doRecover',    't' => 'Recover Deleted Items','h'=>'If set, the deleted items will be recovered', 'f' => 'boolean'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/post/{id}/comments'         => [
                    'title' => 'Fetches post comments',
                    'method' => 'GET',
                    'auth'  => true,
                    'params'                => [['n' => 'id', 't' => 'Post Id', 'f' => 'integer', 'in' => 'path', 'd' => $defaultPostId]],
                    'responses'             => ['200' => ['s' => 'Comment']]
                ],

                '/post/{post-id}/write-comment'   => [
                    'title' => 'Creates or updates comment',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                        ['n' => 'post-id',     't' => 'Post Id',             'f' => 'integer', 'in' => 'path', 'd' => $defaultPostId],
                        ['n' => 'id' ,          't' => 'Comment Id To Update', 'o' => true, 'f' => 'integer'],
                        ['n' => 'body' ,        't' => 'Comment Text',         'f' => 'string'],
                        ['n' => 'parentId' ,    't' => 'Parent Comment Id',    'o' => true, 'f' => 'integer'],
                    ],
                    'responses'             => ['200' => ['s' => 'Comment']]
                ],
            ]
        ];
    }

    protected function getModelClass() {
        return ApiPost::className();
    }

    /**
     * Fetches post data
     *
     * @param int $id
     */
    public function actionGet($id = null) {
        $this->addContent($this->checkModelPermission(intval($id), IPermissions::permRead));
    }

	/**
	 * Create or update the post
	 *
	 * @param string $username
	 */
	public function actionWrite($id = null) {
		if ($id) {
			$model = $this->checkModelPermission(intval($id), IPermissions::permWrite);
		} else {
            $model = new ApiPost();
		}

		$model->load(Helpers::getRequestParams('post'));
        $model->blog_id = 1;

        $this->checkParentModelPermission($model->blog_id, IPermissions::permWrite, ['parentModelClass' => ApiBlog::className()]);

		$model->save();

		$this->addContent($model);
	}

    /**
    * Marks post for deletion
    *
    * @param int $id
    */
    public function actionDeleteRecover($id, $doRecover = false) {
        $post = $this->checkModelPermission(intval($id), IPermissions::permWrite);

        if (!$doRecover) {
            $this->addContent($post->markDeleted());
        } else {
            $this->addContent($post->undelete()); 
        }
    }

    /**
     * List posts
     *
     * @param int $page
     * @param int $maxItems
     */
    public function actionList($username = null, $page = 1, $maxItems = 10) {
        if ($maxItems > 500) $maxItems = 500;
        $user = Yii::$app->user;

        // Fetch my Stories
        if ($username == 'me' || ($username && $user->identity->username == $username)) {
            $targetUser = ApiUser::getActiveUser($user->identity->id);

            $stories = $targetUser->getStories()->offset(($page - 1) * $maxItems)->limit($maxItems)->all();;
        // Fetch Someone's else stories
        } elseif ($username) {
            $targetUser = ApiUser::getActiveUser($username);

            if (!$targetUser) {
                $this->addErrorMessage('User ' . $username . ' is not available');
                
                return;
            }

            $stories = $targetUser->getStories()->offset(($page - 1) * $maxItems)->limit($maxItems)->all();
        // Fetch all stories
        } else {
            $conditions = ['status' => 0];
            $stories = ApiPost::find()->where($conditions)->orderBy('time_published')->offset(($page - 1) * $maxItems)->limit($maxItems)->all();
        }

        foreach ($stories as $post) {
            $post->setScenario('listView');
            $post->calculateProgress();
        }

        $this->addContent($stories);
    } 
}
