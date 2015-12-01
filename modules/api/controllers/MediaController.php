<?php

namespace app\modules\api\controllers;

use Yii;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\Ml;
use app\components\interfaces\IPermissions;
use app\models\User;
use app\models\Story;
use app\modules\api\models\ApiMedia;
use app\modules\api\models\ApiUser;
use app\modules\api\models\ApiStory;
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

         $b['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'upload' => ['post'],
            ],
        ];

        return $b;
    }

    protected function getModelClass() {
        return ApiMedia::className();
    }

    /**
     * Log in
     *
     * @param string $username
     */
    public function actionUpload() {
        $form = new ApiMediaForm();

        $form->load(Helpers::getRequestParams('post'));
        $form->file = UploadedFile::getInstance($form, 'file');
 
        if ($form->validate()) {
            // Userpic
            if ($form->targetType == ApiUser::typeId) {
                $parent = $this->checkParentModelPermission($form->targetId, IPermissions::permWrite, ['parentModelClass' => ApiUser::className()]);

                $model = $parent->addMedia($form->file, $form->mediaType, new ApiMedia());
            // Story Image
            } elseif ($form->targetType == ApiStory::typeId) {
                $parent = $this->checkParentModelPermission($form->targetId, 'write', ['parentModelClass' => ApiStory::className()]);
                if (!$parent->isValidDate($form->date)) throw new \Exception(Ml::t('Invalid story date', 'media')); 

                $model = $parent->addMedia($form->file, $form->mediaType, new ApiMedia(), ['fields' => ['date' => $form->date]]);
            }

            $this->addContent($model);
        } else {
            $this->addContent($form);
        }
    }
}
