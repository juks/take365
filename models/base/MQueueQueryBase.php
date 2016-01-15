<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[Mqueue]].
 *
 * @see Mqueue
 */
class MQueueQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Mqueue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Mqueue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}