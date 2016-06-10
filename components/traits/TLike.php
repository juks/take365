<?php

namespace app\components\traits;

use Yii;
use app\models\Like;
use app\components\Ml;
use app\components\interfaces\IPermissions;


/**
 * Trait implementing like/unlike methods
 */
trait TLike {
    /**
     * Adds or removes like according to given state
     */
    public function setLike($state) {
        $user = Yii::$app->user;
        $result = $this->hasAttribute('likes_count') ? $this->likes_count : null;

        if (!$this->hasPermission($user, IPermissions::permLike)) throw new \app\components\ModelException(Ml::t('Forbidden'));

        $ids = $this->getTargetIdTargetType();
        $data = ['target_id' => $ids[0], 'target_type' => $ids[1]];

        $item = Like::find()->where($data)->one();

        if ($item && $item->is_active == $state || !$item && !$state) return $result;

        if (!$item) {
            if (method_exists($this, 'onFirstTimeLike')) $this->onFirstTimeLike($user);
            $item = new Like($data);
        }

        $item->is_active = intval($state);

        if ($item->save() && $this->hasAttribute('likes_count')) {
            if ($state) $this->likes_count ++;
            else $this->likes_count --;

            $this->save();
            $result = $this->likes_count;
        }

        return $result;
    }

    /**
     * Adds like
     */
    public function like() {
        return $this->setLike(true);
    }

    /**
     * Removes like left by current user
     */
    public function unlike() {
        return $this->setLike(false);
    }

    /**
      Gets the parent object id and type
     */
    public function getTargetIdTargetType() {
        $targetId = $this->hasAttribute('id') ? $this->id : null;
        $targetType = defined('self::typeId') ? self::typeId : null;

        if (!$targetId) throw new \app\components\ModelException(Ml::t('Failed to get target ID'));
        if (!$targetType) throw new \app\components\ModelException(Ml::t('Failed to get target type'));

        return [$targetId, $targetType];
    }
}

?>