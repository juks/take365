<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Story;
use app\models\Mosaic;
use app\models\RegisterForm;
use app\models\Blog;
use app\models\Post;
use app\components\MyController;
use app\components\Captcha;
use app\components\Ml;

class SiteController extends MyController
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'contact'],
                'rules' => [
                    [
                        'actions'   => ['secret'],
                        'allow'     => true,
                        'roles'     => ['admin']
                    ],

                    [
                        'actions'   => ['index', 'auth', 'captcha', 'help', 'howto', 'blog', 'blog-post', 'error', 'cave'],
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
                    'logout' => ['post']
                ],
            ],
        ];
    }

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

    public function oAuthSuccess($client) {
        User::extLogin($client);
        //if (User::extLogin($client)) $this->redirect('/');
    }

    public function actionIndex() {
        $user = Yii::$app->user;
        if (!$user->isGuest) $this->redirect($user->identity->url);

        $mItem = Mosaic::getCurrent();

        if ($mItem) {
            $data = $mItem->parsedData;

            $this->addJsVars([
                                'ids'               => $data['ids'],
                                'urls'              => $data['urls'],
                                'currentMosaicId'   => $mItem->id,
                                'maxSprites'        => Mosaic::thumbLimit,
                                'maxSpritesPerFile' => Mosaic::fileThumbLimit
                            ]);
        }  

        $model = new RegisterForm();
        $this->layout = 'front';

        return $this->render('index', ['model' => $model]);
    }

    public function actionLogin() {        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Lists items in site blog
     */
    public function actionBlog() {
        $this->setTitle(Ml::t('Blog'));

        $blog = Blog::findOne(1);
        $posts = $blog->posts;

        return $this->render('blog', ['posts' => $posts]);
    }

    /**
     * Shows blog post
     *
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionBlogPost($id) {
        $this->setTitle(Ml::t('Blog Post'));

        $post = Post::getActiveItem($id);
        if (!$post) throw new \yii\web\NotFoundHttpException('Здесь ничего нет');

        return $this->render('post', ['post' => $post]);
    }

    public function actionHelp() {
        $this->setTitle(Ml::t('About the project'));
        $sampleStories = Story::find()->where(['status' => Story::statusPublic, 'is_complete' => 1])->orderBy('rand()')->limit(3)->all();

        foreach ($sampleStories as $story) $story->formatShort(['imageLimit' => 10]);

        return $this->render('help', ['sampleStories' => $sampleStories]);
    }

    public function actionHowto() {
        $this->setTitle(Ml::t('Howto'));
        return $this->render('howto');
    }

    public function actionCaptcha() {
        $captcha = new Captcha(6, 0, 5);
        
        @session_start();
        $_SESSION['CAPTCHAString'] = $captcha->getCaptchaString();
        $captcha->makeCaptcha();    
    }
}
