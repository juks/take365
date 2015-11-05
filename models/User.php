<?php

namespace app\models;

use app\models\base\AuthUserBase;
use yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;
use app\components\traits\TCheckField;
use app\components\traits\THasPermission;
use app\components\interfaces\IPermissions;
use app\components\interfaces\IGetType;

class User extends AuthUserBase implements IdentityInterface, IPermissions, IGetType {
    use TCheckField;
    use THasPermission;

    public function getIsPublic() {
        return true;
    }

    public function getCreatorIdField() {
        return 'id';
    }

    public function getType() {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::find()
                ->where([
                    "id" => $id
                ])
                ->one();
    }

    /**
     * @inheritdoc
     */
    /* modified */
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'recovery_code' => $token
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->access_token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return $this->password === md5($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = md5($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Security::generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->recovery_code = Security::generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->recovery_code = null;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            $this->is_active    = true;
            $this->time_created = time();
            $this->ip_created   = ip2long(Yii::$app->request->userIP);
        } else {
            $this->time_updated = time();
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert) {
        if(isset($this->password)) $this->setPassword($this->password);
        return parent::beforeSave($insert);
    }
}

?>