<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Story;
use app\models\Mosaic;
use app\models\RegisterForm;
use app\models\Newsletter;
use app\models\Blog;
use app\models\Post;
use app\components\MyController;
use app\components\Captcha;
use app\components\Ml;

class PanelController extends MyController
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'contact'],
                'rules' => [
                    [
                        'actions'   => ['blog', 'post-write', 'newsletter', 'newsletter-write', 'newsletter-test', 'newsletter-deliver'],
                        'allow'     => true,
                        'roles'     => ['admin']
                    ],

                    [
                        'allow'     => false,
                        'roles'     => ['@']
                    ]
                ],
            ],
        ];
    }

    public function actions() {
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

    /**
     * Show blog entries
     * @return string
     */
    public function actionBlog() {
        $blog = Blog::findOne(1);
        $posts = $blog->getPosts()->with('author')->all();

        return $this->render('blog', ['posts' => $posts]);
    }

    /**
     * Create or update blog entry
     * @param null $id
     * @return string
     */
    public function actionPostWrite($id = null) {
        if ($id) {
            $formTitle = 'Редактировать запись';
            
            $post = Blog::getActivePost($id);
            if (!$post) throw new \yii\web\NotFoundHttpException('Post not found!');
        } else {
            $formTitle = 'Создать запись';
            $post = new Post();
        }

        return $this->render('blogWrite', [
            'formTitle'  => $formTitle,
            'post'       => $post
        ]);
    }

    /**
     * Newsletters list
     */
    public function actionNewsletter() {
        $newsletters = Newsletter::find()->all();

        return $this->render('newsletter', ['newsletters' => $newsletters]);
    }

    /**
     * Newsletter update interface
     * @param null $id
     * @return string
     */
    public function actionNewsletterWrite($id = null) {
        if ($id) {
            $newsletter = Newsletter::findOne($id);
        } else {
            $newsletter = new Newsletter();
        }

        if ($newsletter->load(Yii::$app->request->post())) {
            if ($newsletter->validate()) {
                $newsletter->save();
            }
        }

        return $this->render('newsletterWrite', ['newsletter' => $newsletter]);
    }
}
