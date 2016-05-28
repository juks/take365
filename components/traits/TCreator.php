<?php

namespace app\components\traits;

use app\models\User;

/**
 * Trait implementing IAttachTo methods
 */
trait TCreator {
    public $creatorCache = null;

    public function getCreator() {
        if (!$this->creatorCache) {
            $this->creatorCache = $this->hasOne(User::className(), ['id' => 'created_by']);
        }

        return $this->creatorCache;
    }
}

?>