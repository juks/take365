<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[MosaicBase]].
 *
 * @see MosaicBase
 */
class MosaicQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return MosaicBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MosaicBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}