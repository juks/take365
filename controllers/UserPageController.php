<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Story;
use app\models\Media;
use app\components\MyController;
use app\components\interfaces\IPermissions;

class UserPageController extends MyController {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions'   => ['home', 'profile', 'edit', 'story'],
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
        $owner = User::getActiveUser($username);

        if (!$owner) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        $stories = $owner->stories;
        foreach ($stories as $story) $story->formatShort(['imageLimit' => 90]);

        return $this->render('home', [
                                        'owner'          => $owner,
                                        'stories'       => $stories,
                                        'canCreate'     => false,
                                        'pageType'      => 'home'
                                    ]);
    }

    /*
    * Display user profile
    */
    public function actionProfile($username) {
        $user = User::getActiveUser($username);

        if (!$user) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');
        $user->format();

        return $this->render('profile', [
                                        'user'         => $user,
                                        'pageType'     => 'profile'
                                    ]);
    }

    /*
    * Edit user profile
    */
    public function actionEdit($username) {
        $user = User::getActiveUser($username);

        if (!$user) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');
        $user->hasPermission(Yii::$app->user, IPermissions::permWrite);
        $user->format();
        
        return $this->render('edit', [
                                        'user'         => $user,
                                        'pageType'     => 'profile',
                                        'targetId'     => $user->id,
                                        'targetType'   => User::typeId,
                                        'mediaType'    => Media::aliasUserpic
                                    ]);
    }

    /*
    * Display user story
    */
    public function actionStory($username, $storyId) {
        $owner = User::getActiveUser($username);
        if (!$owner) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        $story = Story::getActiveStory($storyId);
        if (!$story || $story->created_by != $owner->id) if (!$owner) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        $story->format();
        $canManage = $story->hasPermission(Yii::$app->user, IPermissions::permWrite);

        $this->addJsVars([  
                            'storyId'       => $storyId,
                            'targetType'    => Story::typeId,
                            'mediaType'     => Media::aliasStoryImage,
                            'canManage'     => $canManage,
                            'storyDeleted'  => $story->isDeleted
                        ]);


        return $this->render('story', [
                                        'user'      => $owner,
                                        'story'     => $story,
                                        'canManage' => $canManage,
                                        'pageType'  => 'story'
                                    ]);
    }
}
