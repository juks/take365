<?php

namespace app\modules\api\controllers;

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
                                'actions' => ['newsletter-test', 'newsletter-deliver'],
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
}
