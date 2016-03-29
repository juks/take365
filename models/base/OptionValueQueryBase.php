<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[OptionValueBase]].
 *
 * @see OptionValueBase
 */
class OptionValueQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return OptionValueBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OptionValueBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}