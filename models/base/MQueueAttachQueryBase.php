<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[MQueueAttachBase]].
 *
 * @see MQueueAttachBase
 */
class MQueueAttachQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return MQueueAttachBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MQueueAttachBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}