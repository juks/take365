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

class PanelController extends MyController
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'contact'],
                'rules' => [
                    [
                        'actions'   => ['blog', 'write'],
                        'allow'     => true,
                        'roles'     => ['?', 'admin']
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

    public function actionBlog() {
        $blog = Blog::findOne(1);
        $posts = $blog->getPosts()->with('author')->all();

        return $this->render('blog', ['posts' => $posts]);
    }

    public function actionWrite($id = null) {
        if (!$id) {
            $post = new \app\models\Post();
        } else {
            $post = Blog::getActivePost($id);
        }

        if ($post->load(Yii::$app->request->post())) {
            if ($post->validate()) {
                $post->save();
            }
        }

        return $this->render('write', ['post' => $post]);
    }

}
