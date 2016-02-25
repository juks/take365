<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Story;
use app\models\Media;
use app\components\MyController;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\components\Ml;

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

    public function checkUrlUpgrade($user, $username) {
        return substr($username, 0, 1) == '@' && $user->username;
    }

    /*
    * Display user home page
    */
    public function actionHome($username) {
        $owner = User::getActiveUser($username);

        if (!$owner) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');
        
        // Redirect if user was requested by id but has username already
        if ($this->checkUrlUpgrade($owner, $username)) $this->redirect($owner->url);

        $owner->format();
        $this->setTitle(Ml::t('{user} stories', null, ['user' => $owner->fullnameFilled]));

        $stories = $owner->stories;
        foreach ($stories as $story) $story->formatShort(['imageLimit' => 90]);

        return $this->render('home', [
                                        'owner'          => $owner,
                                        'stories'       => $stories,
                                        'canCreate'     => $owner->thisIsMe && Story::checkQuota(),
                                        'pageType'      => 'home'
                                    ]);
    }

    /*
    * Display user profile
    */
    public function actionProfile($username) {
        $owner = User::getActiveUser($username);

        if (!$owner) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        // Redirect if user was requested by id but has username already
        if ($this->checkUrlUpgrade($owner, $username)) $this->redirect($owner->urlProfile);

        $owner->format();
        $this->setTitle(Ml::t('{user} profile page', null, ['user' => $owner->fullnameFilled]));

        return $this->render('profile', [
                                        'user'         => $owner,
                                        'pageType'     => 'profile'
                                    ]);
    }

    /*
    * Edit user profile
    */
    public function actionEdit($username) {
        $owner = User::getActiveUser($username);

        if (!$owner) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        // Redirect if user was requested by id but has username already
        if ($this->checkUrlUpgrade($owner, $username)) $this->redirect($owner->urlEdit);

        $owner->hasPermission(Yii::$app->user, IPermissions::permWrite);
        $owner->format();

        $this->setTitle(Ml::t('Profile update'));

        $timezones = Helpers::listTimezones();

        foreach ($timezones as &$timezone) {
            $timezone['isSelected'] = $owner->timezone == $timezone['id'] || !$owner->timezone && $timezone['id'] == 'none';
        }
        
        return $this->render('edit', [
                                        'user'         => $owner,
                                        'pageType'     => 'profile',
                                        'targetId'     => $owner->id,
                                        'targetType'   => User::typeId,
                                        'mediaType'    => Media::aliasUserpic,
                                        'timezones'    => $timezones
                                    ]);
    }

    /*
    * Display user story
    */
    public function actionStory($username, $storyId) {
        $owner = User::getActiveUser($username);
        if (!$owner) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        $story = Story::getActiveStory($storyId);
        if (!$story || $story->created_by != $owner->id) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        // Redirect if user was requested by id but has username already
        if ($this->checkUrlUpgrade($owner, $username)) $this->redirect($story->url);

        $story->format();
        $this->setTitle($story->titleFilled);

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
