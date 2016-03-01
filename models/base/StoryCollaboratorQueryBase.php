<?php

namespace app\models\base;

/**
 * This is the ActiveQuery class for [[StoryCollaborator]].
 *
 * @see StoryCollaborator
 */
class StoryCollaboratorQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return StoryCollaborator[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StoryCollaborator|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}