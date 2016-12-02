<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[StorageBase]].
 *
 * @see StorageBase
 */
class StorageQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return StorageBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StorageBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}