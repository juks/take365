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
                                'actions' => ['get', 'upload', 'write', 'swap-days', 'delete-recover', 'like', 'unlike', 'list-likes'],
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
                'upload'            => ['post'],
                'write'             => ['post'],
                'swap-days'         => ['post'],
                'delete-recover'    => ['post'],
                'like'              => ['post'],
                'unlike'            => ['post'],
            ],
        ];

        return $b;
    }

    /**
    * Returns the array with the data needed for Swagger UI
    */
    public static function getSwaggerData() {
        $user = Yii::$app->user;

        $defaultUserId = null;
        $defaultStoryId = null;
        $defaultDate = date('Y-m-d', time());

        if (!$user->isGuest) {
            if (!$user->isGuest) {
                $stories = $user->identity->stories;
                if ($stories) {
                    $defaultStoryId = $stories[0]->id;
                }
            }
        }

        return [
            'title'                     => 'Media',
            'description'               => 'Image upload and control methods',
            'methods'                   => [
                '/media/{id}'         => [
                    'title' => 'Fetches media information',
                    'method' => 'GET',
                    'auth'  => false,
                    'params'                => [['n' => 'id', 't' => 'Media Id', 'f' => 'integer', 'in' => 'path', 'd' => 1]],
                    'responses'             => ['200' => ['s' => 'Story']]
                ],

                '/media/player-data'     => [
                    'title' => 'Retrieves images for player',
                    'method' => 'GET',
                    'params'                => [
                                                    ['n' => 'storyId',      't' => 'Story Id',    'h'=>'1 for user, 2 for story', 'f' => 'integer', 'd' => $defaultStoryId],
                                                    ['n' => 'date',         't' => 'Target Date', 'h'=>'', 'f' => 'string', 'd' => $defaultDate],
                                                    ['n' => 'span',         't' => 'Select Span', 'h'=>'Eg. "-10 (left)", "10" (right)', 'f' => 'integer', 'd' => 10],
                                            ],
                    'responses'             => ['200' => ['t' => 'array', 's' => 'Media']]
                ],

                '/media/upload'     => [
                    'auth'  => true,
                    'isMultipart' => true,
                    'title' => '\'Uploads new media resource. Valid target types: "1" for user, "2" for story. Valid media types are: "userpic", "storyImage"\'',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'targetId',     't' => 'Target Object Id',              'h'=>'', 'f' => 'integer', 'd' => $defaultStoryId],
                                                    ['n' => 'targetType',   't' => 'Target Object Type',            'h'=>'1 for user, 2 for story', 'f' => 'integer', 'e' => [2, 1]],
                                                    ['n' => 'mediaType',    't' => 'Type of Uploaded Media',        'h'=>'Eg. "userpic", "storyImage"', 'f' => 'string', 'e' => ['storyImage', 'userpic']],
                                                    ['n' => 'date',         't' => 'Calendar data',                 'h'=>'Only for story images, eg. "' . $defaultDate . '"', 'f' => 'string', 'd' => $defaultDate],
                                                    ['n' => 'autoDate',     't' => 'Date autodetection',            'h'=>'Enable date detection for batch upload (based on exif data)', 'f'=>'boolean', 'd' => 'false', 'e' => [true, false]],
                                                    ['n' => 'file',         't' => 'Media Resource',                'h'=>'Eg. "userpic", "storyImage"', 'f' => 'file'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Media']]
                ],

                '/media/write'     => [
                    'auth'  => true,
                    'title' => 'Updates media item\'s attributes',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'id',           't' => 'Media Item Id',                'h'=>'', 'f' => 'integer'],
                                                    ['n' => 'title',        't' => 'New Title',                    'h'=>'', 'f' => 'string'],
                                                    ['n' => 'description',  't' => 'New Description',              'h'=>'', 'f' => 'string'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Media']]
                ],

                '/media/swap-days'     => [
                    'auth'  => true,
                    'title' => 'Swaps the date of two story images',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'storyId',        't' => 'The story id', 'f' => 'integer'],
                                                    ['n' => 'dateA',          't' => 'The first item date',             'h'=>'Eg. "2015-05-20"', 'f' => 'string'],
                                                    ['n' => 'dateB',          't' => 'The second item date',            'h'=>'Eg. "2015-05-15"', 'f' => 'string'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/media/delete-recover'     => [
                    'auth'  => true,
                    'title' => 'Deletes or recovers media items',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'idString',     't' => 'Media Items Identifiers',      'h'=>'Eg. "1,2,3"', 'f' => 'string'],
                                                    ['n' => 'doRecover',    't' => 'Recover Deleted Items',        'h'=>'If set, the deleted items will be recovered', 'f' => 'boolean'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],

                '/media/{id}/comments'         => [
                    'title' => 'Fetches media comments',
                    'method' => 'GET',
                    'auth'  => false,
                    'params'                => [
                        ['n' => 'id', 't' => 'Media Id', 'f' => 'integer', 'in' => 'path', 'd' => 1],
                        ['n' => 'lastTimestamp','t' => 'Show only comments that were created since given timestamp', 'o' => true, 'f' => 'integer'],
                    ],

                    'responses'             => ['200' => ['s' => 'Comment']]
                ],

                '/media/{media-id}/write-comment'   => [
                    'title' => 'Creates or updates comment',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                        ['n' => 'media-id',     't' => 'Media Id',             'f' => 'integer', 'in' => 'path', 'd' => $defaultStoryId],
                        ['n' => 'id' ,          't' => 'Comment Id To Update', 'o' => true, 'f' => 'integer'],
                        ['n' => 'body' ,        't' => 'Comment Text',         'f' => 'string'],
                        ['n' => 'parentId' ,    't' => 'Parent Comment Id',    'o' => true, 'f' => 'integer'],
                    ],
                    'responses'             => ['200' => ['s' => 'Comment']]
                ],

                '/media/{id}/like'         => [
                    'title' => 'Adds like for the image',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [['n' => 'id', 't' => 'Media Id', 'f' => 'integer', 'in' => 'path', 'd' => 1]],
                    'responses'             => ['200' => ['s' => 'Likes count']]
                ],

                '/media/{id}/unlike'         => [
                    'title' => 'Removes like for the image',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [['n' => 'id', 't' => 'Media Id', 'f' => 'integer', 'in' => 'path', 'd' => 1]],
                    'responses'             => ['200' => ['s' => 'Likes Count']]
                ],

                '/media/{id}/likes'         => [
                    'title' => 'Returns likes list',
                    'method' => 'GET',
                    'auth'  => true,
                    'params'                => [['n' => 'id', 't' => 'Media Id', 'f' => 'integer', 'in' => 'path', 'd' => 1]],
                    'responses'             => ['200' => ['s' => 'Like']]
                ],
            ]
        ];
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

                $mediaItem = $parent->addMedia($form->file, $form->mediaType, new ApiMedia());
            // Story Image
            } elseif ($form->targetType == ApiStory::typeId) {
                // Should we create a new story?
                if ($form->targetId == 0) {
                    if (!$form->date) throw new \app\components\ModelException('No story date', 'date');

                    if (!ApiStory::checkQuota()) {
                        $this->addErrorMessage('За последнее время вы создали слишком много историй');
                        return;
                    }

                    $newStory = new ApiStory();
                    $dateParts = explode('-', $form->date);

                    $newStory->load([
                                    'title'         => 'Без названия',
                                    'time_start'    => mktime(0, 0, 0, $dateParts[1], $dateParts[2], $dateParts[0])
                                ]);

                    if (!$newStory->save()) throw new \app\components\ControllerException("Не удалось создать историю!");

                    $this->addContent($newStory->url, 'redirect');
                    $this->addContent($newStory->id, 'storyId');
                    $form->targetId = $newStory->id;
                }

                $parent = $this->checkParentModelPermission($form->targetId, IPermissions::permWrite, ['parentModelClass' => ApiStory::className()]);

                // Auto date
                if ($form->autoDate && !$form->date) {
                    $autoDate = ApiMedia::getImageDate($form->file->tempName);
                    if (!$autoDate) throw new \app\components\ControllerException(Ml::t('Failed to detect date', 'media'));

                    // No automatic image override
                    if (!$parent->isEmptyDate($autoDate)) return;

                    $form->date = $autoDate;
                }

                if (!$parent->isValidDate($form->date)) throw new \app\components\ControllerException(Ml::t('Invalid story date', 'media'));

                $mediaItem = $parent->addMedia($form->file, $form->mediaType, new ApiMedia(), ['fields' => ['date' => $form->date]]);
            }

            $this->addContent($mediaItem);
        } else {
            $this->addContent($form);
        }
    }

    /**
    * Get media info
    *
    * @param integer $id
    */
    public function actionGet($id) {
        $item = $this->checkModelPermission(intval($id), IPermissions::permRead);

        $this->addContent($item);
    }

    /**
     * Likes media
     *
     * @param integer $id
     */
    public function actionLike($id) {
        $item = $this->checkModelPermission(intval($id), IPermissions::permRead);

        $this->addContent($item->like());
    }

    /**
     * Unlikes media
     *
     * @param integer $id
     */
    public function actionUnlike($id) {
        $item = $this->checkModelPermission(intval($id), IPermissions::permRead);

        $this->addContent($item->unlike());
    }

    /**
     * Unlikes media for edit
     *
     * @param integer $id
     */
    public function actionListLikes($id) {
        $item = $this->checkModelPermission(intval($id), IPermissions::permRead);

        $this->addContent($item->listLikes());
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
        if (!$story->isValidDate($dateA) || !$story->isValidDate($dateB)) throw new \Exception('Invalid date');

        $itemA = ApiMedia::find()->where(['target_id' => $storyId, 'date' => $dateA])->one();
        $itemB = ApiMedia::find()->where(['target_id' => $storyId, 'date' => $dateB])->one();

        if ($itemA && !$itemA->hasPermission(Yii::$app->user, IPermissions::permWrite)) throw new \yii\web\ForbiddenHttpException();
        if ($itemB && !$itemB->hasPermission(Yii::$app->user, IPermissions::permWrite)) throw new \yii\web\ForbiddenHttpException();

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
        $ids = explode(',', $idString);

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
