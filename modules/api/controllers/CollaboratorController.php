<?php

namespace app\modules\api\controllers;

use Yii;
use app\models\StoryCollaborator;
use app\models\Story;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\Ml;
use app\modules\api\models\ApiUser as User;
use app\modules\api\components\ApiController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class CollaboratorController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'actions' => ['add', 'confirm', 'remove', 'collaborators', 'stories'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                        ],
            ];

        $b['verbs'] = [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            //'add'     => ['post'],
                            //'remove'  => ['post'],
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

            if (!$user->isGuest) {
                $stories = $user->identity->stories;
                if ($stories) {
                    $defaultStoryId = $stories[0]->id;
                }
            }
        }

        return [
            'title'                     => 'Collaboration',
            'description'               => 'Collaboration lets multiple users to work on one story',
            'methods'                   => [
                '/collaborator/collaborators'     => [
                    'auth'  => true,
                    'title' => 'Retrieves story collaborators list',
                    'method' => 'GET',
                    'params'                => [
                                                    ['n' => 'storyId',      't' => 'Story id', 'f' => 'integer', 'd' => $defaultStoryId],
                                            ],
                    'responses'             => ['200' => ['t' => 'array', 's' => 'User']]
                ],

                '/collaborator/stories'     => [
                    'auth'  => true,
                    'title' => 'List stories that the given user listed as collaborator for',
                    'method' => 'GET',
                    'params'                => [],
                    'responses'             => ['200' => ['t' => 'array', 's' => 'Story']]
                ],

                '/collaborator/add'     => [
                    'auth'  => true,
                    'title' => 'Add story collaborator',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'username',     't' => 'Username or user id',  'h'=>'', 'f' => 'string', 'd' => $defaultUsername],
                                           ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/collaborator/confirm'     => [
                    'auth'  => true,
                    'title' => 'Confirm pending collaboration',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'username',      't' => 'Username or user id', 'h'=>'', 'f' => 'string', 'd' => $defaultUsername],
                                           ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/collaborator/remove'     => [
                    'auth'  => true,
                    'title' => 'Remove story collaborator',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'username',      't' => 'Username or user id', 'h'=>'', 'f' => 'string', 'd' => $defaultUsername],
                                           ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],
            ]
        ];
    }

    protected function getModelClass() {
        throw new Exception("Method getModelClass() is not supported by this controller");
    }

    /**
     * List collaborators
     *
     * @param string $username
     */
    public function actionCollaborators($storyId) {
        $story = Story::getActiveStory($storyId);

        if (!$story) throw new \yii\web\NotFoundHttpException(Ml::t('Story not found'));

        $this->addContent(StoryCollaborator::listCollaborators($story));
    }


    /**
     * List collaborators
     *
     * @param string $username
     */
    public function actionStories() {
        $this->addContent(StoryCollaborator::listStories(Yii::$app->user));
    }

    /**
     * Add collaborator
     *
     * @param string $username
     */
    public function actionAdd($storyId, $username) {
        $story = Story::getActiveStory($storyId);
        $user = User::getActiveUser($username);

        if (!$story) throw new \yii\web\NotFoundHttpException(Ml::t('Story not found'));
        if (!$user) throw new \yii\web\NotFoundHttpException(Ml::t('User not found'));

        $this->addContent(StoryCollaborator::add($story, $user));
    }

    /**
     * Add collaborator
     *
     * @param string $username
     */
    public function actionConfirm($storyId, $username) {
        $story = Story::findOne($storyId);
        $user = User::getActiveUser($username);

        if (!$story) throw new \yii\web\NotFoundHttpException(Ml::t('Story not found'));
        if (!$user) throw new \yii\web\NotFoundHttpException(Ml::t('User not found'));

        $this->addContent(StoryCollaborator::confirm($story, $user));
    }

    /**
     * Remove Collaborator
     *
     * @param string $username
     */
    public function actionRemove($storyId, $username) {
        $story = Story::getActiveStory($storyId);
        $user = User::getActiveUser($username);

        if (!$story) throw new \yii\web\NotFoundHttpException(Ml::t('Story not found'));
        if (!$user) throw new \yii\web\NotFoundHttpException(Ml::t('User not found'));

        $this->addContent(StoryCollaborator::remove($story, $user));
    }
}
