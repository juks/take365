<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[AuthUserBase]].
 *
 * @see AuthUserBase
 */
class AuthUserQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return AuthUserBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AuthUserBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}