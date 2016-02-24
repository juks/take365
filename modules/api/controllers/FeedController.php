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
     * Unfollow user
     *
     * @param string $username
     */
    public function actionFeed() {
        $this->addContent(Feed::feed(Yii::$app->user));
    }
}
