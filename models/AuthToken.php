<?php

namespace app\models;

use app\models\base\AuthTokenBase;
use app\models\User;
use Yii;
use app\components\Helpers;
use app\components\traits\TModelExtra;

/**
 * Story class
 */
class AuthToken extends AuthTokenBase {
    use TModelExtra;

    const lifetime = 86400 * 30;

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            $this->time_created = time();
            $this->time_used = time();
            if (!$this->key) $this->key = $this->getCode();
        }

        return parent::beforeValidate();
    }

    public function getCode() {
        return (Helpers::randomString(32) . '-' . time());
    }

    static function getToken($key, $extra = null) {
        $t = static::findOne(['key' => $key]);
        if ($t && !$t->checkExpired()) {
            if (empty($extra['noTouch'])) $t->touch();
            return $t;
        } else {
            return null;
        }
    }

    static function issueToken($user) {
        $t = new AuthToken([
                                'user_id'     => $user->id,
                                'ip_created'  => ip2long(Yii::$app->request->userIP),
                                'time_expire' => time() + self::lifetime
                            ]);

        if (!$t->save()) {
            throw new \Exception("Failed to save token");
        }
        
        return $t;
    }

    public function flush() {
        AuthToken::sqlDelete(['time_expire' => ['<', time()]]);
    }

    public function touch() {
        $this->time_used = time();
        $this->save();
    }

    public function checkExpired() {
        return $this->time_expire !== 0 && $this->time_expire < time();
    }

    public function getUsername() {
        return User::findOne($this->user_id)->username;
    }
}