<?php

namespace app\modules\api\controllers;

use Yii;
use app\models\Feed;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\Ml;
use app\modules\api\models\ApiUser as User;
use app\modules\api\components\ApiController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class FeedController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'actions' => ['follow', 'unfollow', 'is-following', 'feed'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                        ],
            ];

        $b['verbs'] = [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            //'addReader'     => ['post'],
                            //'removeReader'  => ['post'],
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
            'title'                     => 'Feed',
            'description'               => 'The feed of images, uploaded by those current user is subscribed for.',
            'methods'                   => [
                '/feed/feed'     => [
                    'auth'  => true,
                    'title' => 'Retrieves current user\'s feed',
                    'method' => 'GET',
                    'params'                => [
                                                    ['n' => 'page',     't' => 'Page Number',        'h'=>'Eg. 1', 'f' => 'integer', 'o' => true, 'd' => 1],
                                                    ['n' => 'maxItems', 't' => 'Max Items Per Page', 'h'=>'Eg. 10 (max 100)', 'f' => 'integer', 'o' => true, 'd' => 10],
                                            ],
                    'responses'             => ['200' => ['t' => 'array', 's' => 'Media']]
                ],

                '/feed/follow'     => [
                    'auth'  => true,
                    'title' => 'Follow user',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'username', 't' => 'Username or user id', 'h'=>'', 'f' => 'string', 'd' => $defaultUsername],
                                           ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/feed/unfollow'     => [
                    'auth'  => true,
                    'title' => 'Unfollow user',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'username',      't' => 'Username or user id', 'h'=>'', 'f' => 'string', 'd' => $defaultUsername],
                                           ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],


                '/feed/is-following'     => [
                    'auth'  => true,
                    'title' => 'Checks if current users follows other user',
                    'method' => 'GET',
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
     * Follow user
     *
     * @param string $username
     */
    public function actionFollow($username) {
        $user = User::getActiveUser($username);

        if (!$user) throw new \yii\web\NotFoundHttpException(Ml::t('User not found'));

        $this->addContent(Feed::follow($user, Yii::$app->user));
    }

    /**
     * Unfollow user
     *
     * @param string $username
     */
    public function actionUnfollow($username) {
        $user = User::getActiveUser($username);

        if (!$user) throw new \yii\web\NotFoundHttpException(Ml::t('User not found'));

        $this->addContent(Feed::unfollow($user, Yii::$app->user));
    }

    /**
     * Unfollow user
     *
     * @param string $username
     */
    public function actionIsFollowing($username) {
        $user = User::getActiveUser($username);

        if (!$user) throw new \yii\web\NotFoundHttpException(Ml::t('User not found'));

        $this->addContent(Feed::isFollowing($user, Yii::$app->user));
    }

    /**
     * Get user's feed
     *
     * @param string $username
     */
    public function actionFeed($page = 1, $maxItems = 20) {
        $this->addContent(Feed::feed(Yii::$app->user, ['page' => $page, 'maxItems' => $maxItems])['list']);
    }
}
