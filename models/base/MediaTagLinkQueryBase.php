<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[MediaTagLinkBase]].
 *
 * @see MediaTagLinkBase
 */
class MediaTagLinkQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return MediaTagLinkBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MediaTagLinkBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}