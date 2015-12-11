<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Story;
use app\components\MyController;
use app\components\interfaces\IPermissions;

class UserPageController extends MyController {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions'   => ['home', 'profile', 'story'],
                        'allow'     => true,
                        'roles'     => ['?', '@']
                    ],

                    [
                        'allow'     => false,
                        'roles'     => ['@']
                    ]
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'one' => ['post']
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /*
    * Display user home page
    */
    public function actionHome($username) {
        $user = User::getActiveUser($username);

        if (!$user) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        return $this->render('home', [
                                        'user'      => $user,
                                        'stories'   => $user->stories,
                                        'canCreate' => false
                                    ]);
    }

    /*
    * Display user profile
    */
    public function actionProfile($username) {
        $user = User::getActiveUser($username);

        if (!$user) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        return $this->render('profile', [
                                        'user'      => $user,
                                    ]);
    }

    /*
    * Display user profile
    */
    public function actionStory($username, $storyId) {
        $user = User::getActiveUser($username);
        if (!$user) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        $story = Story::getActiveStory($storyId);
        if (!$story || $story->created_by != $user->id) if (!$user) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        $story->format();

        return $this->render('story', [
                                        'user'      => $user,
                                        'story'     => $story,
                                        'canManage' => $story->hasPermission(Yii::$app->user, IPermissions::permWrite)
                                    ]);
    }
}
