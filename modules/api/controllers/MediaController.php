<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\models\User;
use app\models\Story;
use app\modules\api\models\ApiMedia as Media;
use app\modules\api\models\ApiMediaForm;
use app\modules\api\components\ApiController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class MediaController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'actions' => ['upload'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],

                            [
                                'allow' => false,
                                'roles' => ['@']
                            ]
                        ],
            ];

        return $b;
    }

    protected function getModelClass() {
        return ApiMedias::className();
    }

    /**
     * Log in
     *
     * @param string $username
     */
    public function actionUpload() {
        $model = new ApiMediaForm();

        $model->load(Helpers::getRequestParams('post'));

        $model->file = UploadedFile::getInstance($model, 'file');
        
        if ($model->validate()) {
            if ($model->targetType == User::typeId) {
                $target = $this->checkModelPermission($model->targetId, 'write', null, new User());
            } elseif ($model->targetType == Story::typeId) {
                $target = $this->checkModelPermission($model->targetId, 'write', null, new Story());
            }

            $target->addMedia($model->file, $model->mediaType);

        } else {
            $this->addContent($model);
        }
    }
}
