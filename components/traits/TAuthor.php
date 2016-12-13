<?php

namespace app\components\traits;

use app\models\User;

trait TAuthor {
    public $authorCache = null;

    public function getAuthor() {
        if (!$this->authorCache) {
            $this->authorCache = $this->hasOne(User::className(), ['id' => 'created_by']);
        }

        return $this->authorCache;
    }
}

?>