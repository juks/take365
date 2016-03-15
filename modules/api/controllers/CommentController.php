<?php

namespace app\modules\api\controllers;

use Yii;
use app\models\Feed;
use app\components\MyJsonController;
use app\components\Helpers;
use app\components\Ml;
use app\modules\api\models\ApiStory;
use app\modules\api\models\ApiComment;
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
                                'actions' => ['write', 'delete-recover', 'list'],
                                'allow' => true,
                                'roles' => ['@'],
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

    protected function getModelClass() {
        throw new Exception("Method getModelClass() is not supported by this controller");
    }

    /**
     * Create or update comment
     *
     * @param string $username
     */
    public function actionWrite() {
        $form = new \app\modules\api\models\ApiCommentForm();
        $target = null;

        if ($form->load(Helpers::getRequestParams('post')) && $form->validate()) {
            if ($form->targetType == ApiStory::typeId) {
                $target = ApiStory::getActiveStory($form->targetId);
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

            if (Yii::$app->request->isAjax) $this->addContent(['html'=>$this->renderPartial('//blocks/comment.php', ['comment' => $comment])]);
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

    /**
     * List comments
     *
     * @param string $username
     */
    public function actionList($targetType, $targetId) {

    }
}
