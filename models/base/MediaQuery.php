<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[MediaBase]].
 *
 * @see MediaBase
 */
class MediaQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return MediaBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MediaBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}