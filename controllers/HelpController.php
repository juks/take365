<?php

namespace app\controllers;

use app\models\MQueue;
use app\models\MQueueAttach;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Story;
use app\models\Mosaic;
use app\models\RegisterForm;
use app\models\Blog;
use app\models\Post;
use \app\models\Option;
use app\components\MyController;
use app\components\Captcha;
use app\components\Ml;

class HelpController extends MyController
{


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'oAuthSuccess'],
            ],
        ];
    }

    public function actionIndex() {
        $this->setTitle(Ml::t('About the project'));
        $sampleStories = Story::find()->where(['status' => Story::statusPublic, 'is_complete' => 1])->orderBy('rand()')->limit(3)->all();

        foreach ($sampleStories as $story) $story->formatShort(['imageLimit' => 10]);

        return $this->render('help', ['sampleStories' => $sampleStories]);
    }

    public function actionHowto() {
        $this->setTitle(Ml::t('Howto'));
        return $this->render('howto');
    }

    public function actionPrivacy() {
        $this->setTitle(Ml::t('Privacy policy'));
        return $this->render('privacy');
    }
}
