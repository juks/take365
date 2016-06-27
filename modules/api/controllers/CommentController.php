<?php

namespace app\modules\api\controllers;

use Yii;
use app\models\Feed;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\Ml;
use app\modules\api\models\ApiStory;
use app\modules\api\models\ApiComment;
use app\modules\api\models\ApiMedia;
use app\modules\api\components\ApiController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class CommentController extends ApiController {
    public function behaviors() {
        $b = parent::behaviors();

        $b['access'] = [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'actions' => ['write', 'delete-recover'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],

                            [
                                'actions' => ['list-comments'],
                                'allow' => true,
                                'roles' => ['?', '@'],
                            ],
                        ],
            ];

        $b['verbs'] = [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            //'write'     => ['post'],
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
        $defaultUsername = null;
        $defaultEmail = null;
        $defaultStoryId = null;

        if (!$user->isGuest) {
            $defaultUserId = $user->id;
            $defaultUsername = !empty($user->identity->username) ? $user->identity->username : null;
            $defaultEmail = isset($user->identity->email) ? $user->identity->email : null;

            if (!$user->isGuest) {
                $stories = $user->identity->stories;
                if ($stories) {
                    $defaultStoryId = $stories[0]->id;
                }
            }
        }

        return [
            'title'                     => 'Comments',
            'description'               => 'Comments are easy thing to do.',
            'methods'                   => [
                '/comment/list-comments'   => [
                    'title' => 'Retrieve target comments',
                    'method' => 'GET',
                    'params'                => [
                                                    ['n' => 'targetType',   't' => 'Commentable Target Type', 'f' => 'integer', 'e' => [2]],
                                                    ['n' => 'targetId' ,    't' => 'Commentable Id', 'f' => 'integer', 'd' => $defaultStoryId],
                                                    ['n' => 'lastTimestamp','t' => 'Show only comments that were created since given timestamp', 'o' => true, 'f' => 'integer'],
                                            ],
                    'responses'             => ['200' => ['t' => 'array', 's' => 'Comment']]
                ],

                '/comment/write'   => [
                    'title' => 'Creates or updates comment',
                    'method' => 'POST',
                    'auth'  => true,
                    'params'                => [
                                                    ['n' => 'targetType',   't' => 'Commentable Target Type',      'o' => true, 'f' => 'integer', 'h' => '2 for story', 'e' => [2]],
                                                    ['n' => 'targetId' ,    't' => 'Commentable Id',               'o' => true, 'f' => 'integer', 'd' => $defaultStoryId],
                                                    ['n' => 'id' ,          't' => 'Comment Id To Update',         'o' => true, 'f' => 'integer'],
                                                    ['n' => 'body' ,        't' => 'Comment Text',                 'f' => 'string'],
                                                    ['n' => 'parentId' ,    't' => 'Parent Comment Id',            'o' => true, 'f' => 'integer'],
                                            ],
                    'responses'             => ['200' => ['s' => 'Comment']]
                ],

                '/comment/delete-recover'     => [
                    'auth'  => true,
                    'title' => 'Deletes or recovers comments',
                    'method' => 'POST',
                    'params'                => [
                                                    ['n' => 'id', 't' => 'Comment Id', 'f' => 'integer']
                                            ],
                    'responses'             => ['200' => ['s' => 'Response']]
                ],
            ]
        ];
    }

    protected function getModelClass() {
        throw new Exception("Method getModelClass() is not supported by this controller");
    }

    /**
     * List comments
     *
     * @param string $username
     */
    public function actionListComments($targetType, $targetId, $lastTimestamp = null) {
        if ($targetType == ApiStory::typeId) {
            $target = ApiStory::getActiveItem($targetId);
        } else if ($targetType == ApiMedia::typeId) {
            $target = ApiMedia::getActiveItem($targetId);
        } else {
            throw new \app\components\ControllerException('Invalid target type');
        }

        if (!$target) throw new \app\components\ControllerException(Ml::t('Object not found'));

        $this->addContent($target->getComments($lastTimestamp));
    }

    /**
     * Create or update comment
     *
     * @param string $username
     */
    public function actionWrite($targetId = null, $targetType = null) {
        $form = new \app\modules\api\models\ApiCommentForm();
        $target = null;

        // If some of parameters were set internally
        if ($targetId) $form->targetId = $targetId;
        if ($targetType) $form->targetType = $targetType;

        if ($form->load(Helpers::getRequestParams('post')) && $form->validate()) {
            if ($form->targetType == ApiStory::typeId) {
                $target = ApiStory::getActiveItem($form->targetId);
            } elseif ($form->id) {
                $comment = ApiComment::findOne($form->id);
                if (!$comment) throw new \app\components\ControllerException(Ml::t('Object not found'));
                $target = $comment->target;
            } else {
                throw new \app\components\ControllerException('Invalid target type');
            }

            if (!$target) throw new \app\components\ControllerException(Ml::t('Object not found'));
            $comment = $target->writeComment($form);

            $this->addContent($comment);
        } else {
            $this->addContent($form);
        }
    }

    /**
     * Deletes and recovers comments
     *
     * @param integer $id
     */
    public function actionDeleteRecover($id) {
        $this->addContent(ApiComment::deleteRecover($id));
    }
}
