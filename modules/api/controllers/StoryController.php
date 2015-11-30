<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\modules\api\components\ApiController;
use app\modules\api\models\ApiStory;
use app\modules\api\models\ApiUser;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class StoryController extends ApiController {
    public function behaviors()
    {
        $b = parent::behaviors();

        $b['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['write', 'list'],
                    'allow' => true,
                    'roles' => ['@'],
                ],

                [
                    'actions' => ['get'],
                    'allow' => true,
                    'roles' => ['?', '@'],
                ],

                [
                    'allow' => false,
                    'roles' => ['@']
                ]
            ],
        ];

        $b['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'funkout' => ['post'],
            ],
        ];

        return $b;
    }

    protected function getModelClass() {
        return ApiStory::className();
    }

    /**
     * Fetches story data
     *
     * @param int $id
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

    /**
     * Lists users
     *
     * @param int $page
     * @param int $maxItems
     */
    public function actionList($username = null, $page = 1, $maxItems = 10) {
        if ($maxItems > 500) $maxItems = 500;
        $user = Yii::$app->user;

        // Fetch my Stories
        if ($username == 'me' || ($username && $user->identity->username == $username)) {
            $conditions = ['created_by' => $user->identity->id];
        // Fetch Someone's else stories
        } elseif ($username) {
            $targetUser = ApiUSer::find()->where(['username' => $username])->andWhere(ApiUSer::getActiveCondition())->one();

            if (!$targetUser) {
                $this->addErrorMessage('User ' . $username . ' is not available');
                
                return;
            } else {
                $conditions = ['status' => 0, 'created_by' =>  $targetUser->id];
            }
        // Fetch all stories
        } else {
            $conditions = ['status' => 0];
        }

        $stories = ApiStory::find()->where($conditions)->orderBy('time_published')->offset(($page - 1) * $maxItems)->limit($maxItems)->all();

        foreach ($stories as $story) $story->setScenario('listView');

        $this->addContent($stories);
    } 
}
