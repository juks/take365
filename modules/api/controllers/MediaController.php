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
                                'actions' => ['upload', 'write', 'swap-days', 'delete-recover'],
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
    public function actionSwapDays($idA, $idB) {
        $itemA = $this->checkModelPermission(intval($idA), IPermissions::permWrite);
        $itemB = $this->checkModelPermission(intval($idB), IPermissions::permWrite);

        if ($itemA->target_id != $itemB->target_id) throw new \Exception("Items hav different target ID");
        
        $story = $this->checkParentModelPermission($itemA->target_id, IPermissions::permWrite, ['parentModelClass' => ApiStory::className()]);

        if (!$story->isValidDate($itemA->date) || !$story->isValidDate($itemB->date)) throw new Exception('Invalid date problem');

        $swapDate = $itemB->date;
        $itemB->date = $itemA->date;
        $itemB->save();

        $itemA->date = $swapDate;
        $itemA->save();
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
