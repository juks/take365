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
                        'actions'   => ['index', 'register', 'auth', 'captcha', 'help', 'howto', 'blog', 'blog-post', 'tag', 'error', 'cave', 'unsubscribe'],
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

    public function actionRegister() {
        return $this->render('register');
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

    public function actionTag($name) {
        $mediaList = \app\models\MediaTagLink::listByTag($name);
        return $this->render('tag', ['mediaList' => $mediaList]);
    }

    public function actionCaptcha() {
        $captcha = new Captcha(6, 0, 5);
        
        @session_start();
        $_SESSION['CAPTCHAString'] = $captcha->getCaptchaString();
        $captcha->makeCaptcha();    
    }

    public function actionUnsubscribe($id, $code, $optionName, $toggle = false) {
        $user = User::getActiveUser(intval($id));

        if (!$user)                                     throw new \yii\web\NotFoundHttpException(Ml::t('Object not found'));
        if (!Option::findOne(['name' => $optionName]))  throw new \yii\web\NotFoundHttpException(Ml::t('Option not found'));
        if ($user->option_code != $code)                throw new \yii\web\ForbiddenHttpException(Ml::t('Forbidden'));

        $actionResult = null;

        $actionStrings = [
            Option::oNotify => [
                [
                    'result' => 'Email-уведомления о событиях выключены.',
                    'title'  => 'Включить email-уведомления о событиях.'
                ],

                [
                    'result' => 'Email-уведомления о событиях включены.',
                    'title'  => 'Отказаться от получения email-уведомлений о событиях.'
                ],
            ],

            Option::oNewsletter => [
                [
                    'result' => 'Подписка на новости отключена.',
                    'title'  => 'Подписаться на нововсти проекта.'
                ],

                [
                    'result' => 'Подписка на новости включена.',
                    'title'  => 'Отказаться от получения нововстей проекта.'
                ],
            ],
        ];

        $value = $user->getOptionValue($optionName);

        if ($toggle) {
            $user->setOptionValue($optionName, $value ? false : true);
            $value = $value ? 0 : 1;
            $actionResult = $actionStrings[$optionName][$value]['result'];
        }

        $actionTitle = $actionStrings[$optionName][$value]['title'];

        return $this->render('unsubscribe', [
                                                'actionResult'  => $actionResult,
                                                'actionTitle'   => $actionTitle,
                                                'actionUrl'     => $user->getUrlUnsubscribe($optionName) . '&toggle=true']);
    }

    public function actionCave() {
        /*$a = \app\models\Storage::getByKey('logo');

        \app\models\MQueue::compose()
            ->toUser(Yii::$app->user->identity, ['checkOption' => 'notify'])
            ->subject('Test')
            ->body('foo')
            ->attach($a)
            ->send();*/

        $nl = \app\models\Newsletter::findOne(1);
        $nl->testDeliver();

        //$s = new \app\models\Storage();
        //$s->takeFile('http://pagemywork.com/filestorage/p1/d3/d9/10/logo.jpg');
    }
}
