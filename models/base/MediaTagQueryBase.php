<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[MediaTagBase]].
 *
 * @see MediaTagBase
 */
class MediaTagQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return MediaTagBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MediaTagBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}