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
