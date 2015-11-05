<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[StoryBase]].
 *
 * @see StoryBase
 */
class StoryQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return StoryBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StoryBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}