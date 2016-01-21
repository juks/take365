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
                                'actions' => ['get', 'upload', 'write', 'swap-days', 'delete-recover'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],

                            [
                                'actions' => ['player-data'],
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
                'upload' => ['post'],
            ],
        ];

        return $b;
    }

    protected function getModelClass() {
        return ApiMedia::className();
    }

    /**
    * Get player data
    *
    * @param string $date
    * @param int $storyId
    * @param int $span
    */
    public function actionPlayerData($storyId, $date, $span) {
        $story = $this->checkParentModelPermission($storyId, IPermissions::permRead, ['parentModelClass' => Story::className()]);
        if (!$story->isValidDate($date)) throw new \yii\web\BadRequestHttpException('Bad date');

        $this->addContent(ApiMedia::getPlayerData(['date' => $date, 'storyId' => $storyId, 'span' => $span]));
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
                if ($form->targetId === 0) {
                    if (!ApiStory::checkQuota()) {
                        $this->addErrorMessage('За последнее время мы создали слишком много историй');
                        return;
                    }

                    $model = new ApiStory();
                    $model->load(['title' => 'Новая']);
                    
                    if (!$model->save()) throw new \app\components\ControllerException("Не удалось создать историю!");

                    $this->addContent('redirect', $model->url);

                    $form->targetId = $model->id();
                }

                $parent = $this->checkParentModelPermission($form->targetId, 'write', ['parentModelClass' => ApiStory::className()]);
                if (!$parent->isValidDate($form->date)) throw new \Exception(Ml::t('Invalid story date', 'media'));

                $model = $parent->addMedia($form->file, $form->mediaType, new ApiMedia(), ['fields' => ['date' => $form->date]]);
            }

            $this->addContent($model);
        } else {
            $this->addContent($form);
        }
    }

    /**
    * Get media for edit
    *
    * @param integer $id
    */
    public function actionGet($id) {
        $item = $this->checkModelPermission(intval($id), IPermissions::permWrite);

        $this->addContent($item);
    }

    /**
    * Updates media ldap_get_attributes(link_identifier, result_entry_identifier)
    *
    * @param integer $id
    * @param string $title
    * @param string $description
    */
    public function actionWrite($id, $title, $description) {
        $item = $this->checkModelPermission(intval($id), IPermissions::permWrite);

        $item->load(Helpers::getRequestParams('post'));
        $item->save();

        $this->addContent($item);
    }

    /**
    * Swaps the date of two media items
    *
    * @param string $idString
    * @param boolean $doRecover
    */
    public function actionSwapDays($storyId, $dateA, $dateB) {
        $story = $this->checkParentModelPermission($storyId, IPermissions::permWrite, ['parentModelClass' => ApiStory::className()]);
        if (!$story->isValidDate($dateA) || !$story->isValidDate($dateB)) throw new Exception('Invalid date');

        $itemA = ApiMedia::find()->where(['target_id' => $storyId, 'date' => $dateA])->one();
        $itemB = ApiMedia::find()->where(['target_id' => $storyId, 'date' => $dateB])->one();

        if ($itemA && !$itemA->hasPermission(Yii::$app->user, IPermissions::permWrite)) throw new ForbiddenHttpException();
        if ($itemB && !$itemB->hasPermission(Yii::$app->user, IPermissions::permWrite)) throw new ForbiddenHttpException();

        if ($itemA) {
            $itemA->date = $dateB;
            $itemA->save();
        }

        if ($itemB) {
            $itemB->date = $dateA;
            $itemB->save();
        }
    }

    /**
    * Deletes or recovers media items
    *
    * @param string $idString
    * @param boolean $doRecover
    */
    public function actionDeleteRecover($idString, $doRecover = false) {
        $ids = preg_split('/,/', $idString);

        if (count($ids) > 100) throw new \yii\web\BadRequestHttpException('Too Much');
        $items = [];

        foreach ($ids as $id) {
            $items[] = $this->checkModelPermission(intval($id), IPermissions::permWrite);
        }

        foreach ($items as $item) {
            if (!$item->is_deleted) {
                $item->markDeleted();
                $this->addContent($item->id);
            } elseif ($doRecover) {
                $item->recoverDeleted();
                $this->addContent($item->id);
            }
        }
    }
}
