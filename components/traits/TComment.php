<?php

namespace app\components\traits;

use Yii;
use app\models\Comment;
use app\components\Ml;
use app\components\interfaces\IPermissions;
use app\components\traits\THasPermission;

trait TComment {
    /**
    * Creates or updates comment
    *
    * @param object $storyId
    **/
    public function writeComment($data) {
        $user = Yii::$app->user;
        if (!$this->hasPermission($user, IPermissions::permComment)) throw new app\components\ModelException(Ml::t('Forbidden'));

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            if ($data->id) {
                $item = Comment::findOne($data->id);

                if (!$item) throw new \app\components\ModelException(Ml::t('Comment not found'));
                if (!$item->hasPermission($user, IPermissions::permWrite)) throw new app\components\ModelException(Ml::t('Comment not found'));

                $item->body = $data->body;
                $item->save();
            } else {
                $treeCondition = [
                                    'target_type' => self::typeId,
                                    'target_id'   => $data->targetId
                                ];

                $item = new Comment([
                                    'target_type'   => self::typeId,
                                    'target_id'     => $data->targetId,
                                    'body'          => $data->body
                                ]);

                if ($data->parentId) {
                    $parentComment = Comment::findOne($data->parentId);
                    if (!$parentComment) throw new \app\components\ModelException('Parent comment not found');

                    $targetRk = $parentComment->rk;

                    $item->level = $parentComment->level + 1;
                    $item->thread = $parentComment->thread;

                    // Update parents
                    Comment::sqlUpdate(
                                    array_merge($treeCondition, ['lk' => ['>', $targetRk]]),
                                    ['lk' => ['+', 2], 'rk' => ['+', 2]]
                                );

                    $item->lk = $targetRk;
                    $item->rk = $targetRk + 1;
                    $item->level = $parentComment->level + 1;
                } else {
                    $targetRk = intval(Comment::sqlGetFuncValue('rk', $treeCondition, 'max')) + 1;
                    $item->lk = $targetRk;
                    $item->rk = $targetRk + 1;
                }

                // Update childs
                Comment::sqlUpdate(
                                array_merge($treeCondition, ['lk' => ['<', $targetRk], 'rk' => ['>=', $targetRk]]),
                                ['rk' => ['+', 2]]
                            );

                $item->save();

                if (!$item->thread) {
                    $item->thread = $item->id;
                    $item->save();
                }

                $this->comments_count ++;
                $this->save();                
            }
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;        
        }

        $transaction->commit();
 
        return $item;
    }

    /**
    * Returns string that reflects actual comments count
    **/
    public function getCommentsCountTitle() {
        return Ml::t('{n,plural,=0{No comments} =1{One Comment} other{# Comments}}', null, ['n' => $this->comments_count]);
    }
}