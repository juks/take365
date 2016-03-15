<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[CommentBase]].
 *
 * @see CommentBase
 */
class CommentQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return CommentBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CommentBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}