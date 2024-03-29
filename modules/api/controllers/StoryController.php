<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\modules\api\components\ApiController;
use app\modules\api\models\ApiStory;
use app\modules\api\models\ApiUser;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class StoryController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['write', 'list', 'list-notify', 'delete-recover'],
                    'allow' => true,
                    'roles' => ['@'],
                ],

                [
                    'actions' => ['get'],
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
        $defaultEmail = null;
        $defaultStoryId = null;

        if (!$user->isGuest) {
            $defaultUserId = $user->id;
            $defaultUsername = !empty($user->identity->username) ? $user->identity->username : null;
            $defaultEmail = isset($user->identity->email) ? $user->identity->email : null;

            $stories = $user->identity->stories;
            if ($stories) {
                $defaultStoryId = $stories[0]->id;
            }
        }

        return [
            'title'                     => 'Stories',
            'description'               => 'Provides read and write access to stories data.',
            'methods'                   => [
                '/story/{id}'         => [
                    'title' => 'Fetches Story information',
                    'method' => 'GET',
                    'auth'  => true,
                    'params'                => [['n' => 'id', 't' => 'Story Id', 'f' => 'integer', 'in' => 'path', 'd' => $defaultStoryId]],
                    'responses'             => ['200' => ['s' => 'Story']]
                ],

                '/story/list'   => [
                    'title' => 'Fetches the list of public or given user stories',
                    'method' => 'GET',
                    'auth'  => true,
                    'params'                => [
                                                    ['n' => 'page',      't' => 'Page Number',                      'o' => true, 'f' => 'integer', 'd' => 1],
                                                    ['n' => 'maxItems' , 't' => 'Maximal Items Count',              'o' => true, 'f' => 'integer', 'd' => 10],
                                                    ['n' => 'username' , 't' => 'Name of User to Fetch Stories of', 'o' => true, 'h' => 'Eg. "bob" for Bob or "me" for current user', 'f' => 'string', 'd' => $defaultUsername]
                                            ],
                    'responses'             => ['200' => ['t' => 'array', 's' => 'Story']]
                ],

                '/story/list-notify'   => [
                    'title' => 'Fetches the list user stories due to a notification for given date',
                    'method' => 'GET',
                    'auth'  => true,
                    'params'                => [
                                                    ['n' => 'date',      't' => 'Current date',                      'f' => 'string'],
                    ],
                    'responses'             => ['200' => ['t' => 'array', 's' => 'Story']]
                ],

                '/story/write'      => [
                    'title' => 'Creates or updates story',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                                                    ['n' => 'id',           't' => 'Story Id',          'o' => true, 'h'=>'If not given, a new story will be created', 'f' => 'integer', 'd' => $defaultStoryId],
                                                    ['n' => 'status',       't' => 'Story Status',      'o' => true, 'h'=>'0 — public, 1 — private', 'f' => 'integer', 'e' => [0, 1]],
                                                    ['n' => 'title',        't' => 'Story Title',       'o' => true, 'f' => 'string'],
                                                    ['n' => 'description',  't' => 'Story Description', 'o' => true, 'f' => 'string'],
                                                    ['n' => 'startDate',    't' => 'The start date (if not specified the current local time date fill be used)', 'o' => true, 'f' => 'string'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Story']]
                ],

                '/story/delete-recover'      => [
                    'title' => 'Deletes or recovers story',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                                                    ['n' => 'id',           't' => 'Story Id',             'h'=>'If not given, a new story will be created', 'f' => 'integer', 'd' => $defaultStoryId],
                                                    ['n' => 'doRecover',    't' => 'Recover Deleted Items','h'=>'If set, the deleted items will be recovered', 'f' => 'boolean'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/story/{id}/comments'         => [
                    'title' => 'Fetches story comments',
                    'method' => 'GET',
                    'auth'  => true,
                    'params'                => [['n' => 'id', 't' => 'Story Id', 'f' => 'integer', 'in' => 'path', 'd' => $defaultStoryId]],
                    'responses'             => ['200' => ['s' => 'Comment']]
                ],

                '/story/{story-id}/write-comment'   => [
                    'title' => 'Creates or updates comment',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                        ['n' => 'story-id',     't' => 'Story Id',             'f' => 'integer', 'in' => 'path', 'd' => $defaultStoryId],
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
        return ApiStory::className();
    }

    /**
     * Fetches story data
     *
     * @param int $id
     */
    public function actionGet($id = null) {
        $this->addContent($this->checkModelPermission(intval($id), IPermissions::permRead));
    }

	/**
	 * Create or update the story
	 *
	 * @param string $username
	 */
	public function actionWrite($id = null) {
		if ($id) {
			$model = $this->checkModelPermission(intval($id), IPermissions::permWrite);
		} else {
            if (!ApiStory::checkQuota()) {
                $this->addErrorMessage('За последнее время вы создали слишком много историй');
                return;
            }

            $model = new ApiStory();
		}

        $params = Helpers::getRequestParams('post');
		$model->load($params);
        if (!empty($params['startDate'])) $model->setStartDate($params['startDate']);
		$model->save();

		$this->addContent($model);
	}

    /**
    * Marks story for deletion
    *
    * @param int $id
    */
    public function actionDeleteRecover($id, $doRecover = false) {
        $story = $this->checkModelPermission(intval($id), IPermissions::permAdmin);

        if (!$doRecover) {
            $this->addContent($story->markDeleted());
        } else {
            $this->addContent($story->undelete()); 
        }
    }

    /**
     * List stories
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
            $stories = ApiStory::find()->where($conditions)->orderBy('time_published')->offset(($page - 1) * $maxItems)->limit($maxItems)->all();
        }

        foreach ($stories as $story) {
            $story->setScenario('listView');
            $story->calculateProgress();
        }

        $this->addContent($stories);
    }

    /**
     * Fetch the list of stories due to by notified
     * @param $date
     */
    public function actionListNotify($date) {
        $this->addContent(Yii::$app->user->identity->getNotifyStories($date));
    }
}
