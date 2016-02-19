<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[FeedBase]].
 *
 * @see FeedBase
 */
class FeedQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return FeedBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return FeedBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}