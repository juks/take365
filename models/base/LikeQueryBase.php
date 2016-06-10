<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[LikeBase]].
 *
 * @see LikeBase
 */
class LikeQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return LikeBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return LikeBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}