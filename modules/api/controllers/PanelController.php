<?php

namespace app\modules\api\controllers;

use app\components\Helpers;
use app\models\Post;
use Yii;
use app\models\Newsletter;
use app\components\Ml;
use app\modules\api\components\ApiController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class PanelController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'actions' => ['newsletter-test', 'newsletter-deliver', 'post-write', 'post-delete'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                        ],
            ];

        $b['verbs'] = [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'newsletter-test'     => ['post'],
                            'newsletter-deliver'  => ['post'],
                        ],
                    ];

        return $b;
    }

    /**
     * Newsletter test delivery
     * @param $id
     * @throws \Exception
     */
    public function actionNewsletterTest($id) {
        $newsletter = Newsletter::findOne($id);

        if ($newsletter) {
            $newsletter->testDeliver();
        }
    }

    /**
     * Newletter mass delivery
     * @param $id
     */
    public function actionNewsletterDeliver($id) {
        $newsletter = Newsletter::findOne($id);

        if ($newsletter) {
            $newsletter->massDeliver();
        }
    }

    /**
     * Create or update blog entry
     * @param null $id
     * @return string
     */
    public function actionPostWrite() {
        $id = Helpers::getRequestParam('Post/id');

        if (!$id) {
            $post = new Post();
        } else {
            $post = Post::getActiveItem($id);
            if ($post === null) throw new \yii\web\NotFoundHttpException('Post not found');
            if ($post === false) throw new \yii\web\ForbiddenHttpException('Forbidden');
        }

        $post->load(Yii::$app->request->post());

        if ($post->validate()) {
            if ($post->save()) $this->addContent($post);
        }
    }

    /**
     * Post deletion
     * @param $id
     * @throws \Exception
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionPostDelete($id) {
        $post = Post::getActiveItem($id);

        if ($post === null) throw new \yii\web\NotFoundHttpException('Post not found');
        if ($post === false) throw new \yii\web\ForbiddenHttpException('Forbidden');

        $post->delete();
    }
}
