<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[BlogBase]].
 *
 * @see BlogBase
 */
class BlogQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return BlogBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return BlogBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}