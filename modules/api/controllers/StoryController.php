<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\modules\api\components\ApiController;
use app\modules\api\models\ApiStory;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class StoryController extends ApiController {
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['writeStory'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                    [
                        'allow' => false,
                        'roles' => ['@']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'funkout' => ['post'],
                ],
            ],
        ];
    }

    protected function getModelClass() {
        return ApiStory::className();
    }

    /**
     * Fetches story data
     *
     * @param string $username
     */
    public function actionGet($id = null) {
        $this->addContent($this->checkModelPermission($id, 'read'));
    }

	/**
	 * Create or update the story
	 *
	 * @param string $username
	 */
	public function actionWrite($id = null) {
		if ($id) {
			$model = $this->checkModelPermission($id, 'write');
		} else {
			$model = new ApiStory();
            if (!$model->checkQuota()) {
                $this->addErrorMessage('Вы создали слишком много историй');
                return;
            }
		}

		$model->load(Helpers::getRequestParams('post'));
		$model->save();

		$this->addContent($model);
	}
}
