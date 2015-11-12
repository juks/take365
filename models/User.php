<?php

namespace app\models;

use app\models\base\AuthUserBase;
use yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use app\components\Helpers;
use yii\web\IdentityInterface;
use app\models\AuthToken;
use app\models\Media;
use app\models\mediaExtra\TMediaUploadExtra;
use app\components\traits\TCheckField;
use app\components\traits\THasPermission;
use app\components\interfaces\IPermissions;
use app\components\interfaces\IGetType;

class User extends AuthUserBase implements IdentityInterface, IPermissions, IGetType {
    use TCheckField;
    use THasPermission;
    use TMediaUploadExtra;

    public $accessToken;
    public $accessTokenExpires;

    const typeId = 1;

    /**
     * Returns public criteria
     */
    public function getIsPublic() {
        return true;
    }

    /**
     * Returns owner id field name
     */
    public function getCreatorIdField() {
        return 'id';
    }

    /**
     * Returns type Id
     */
    public function getType() {
        return self::typeId;
    }

    /**
     * Returns media options
     */
    public function getMediaOptions() {
        return [
                    'userpic' => [
                                        Media::alias                 => 'userpic',
                                        Media::allowedFormats        => array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG),
                                        Media::maxFileSize           => 1048576 * 10,
                                        Media::resizeMode            => Media::resizeMaxSide,
                                        Media::targetDimension       => 3000,
                                        Media::thumbsList            => [
                                                                            Media::resizeMaxSide => [200, 100, 50],
                                                                        ],
                                        Media::quality               => 95,
                                        Media::engine                => Media::engineImageMagick,
                                        Media::resizeFilter          => \Imagick::FILTER_BLACKMAN,
                                        Media::resizeBlur            => 0.86,
                                        Media::thumbQuality          => 96,
                                        Media::saveExif              => true,
                                        Media::autoOrient            => true
                                ]
                ];
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
        $t = AuthToken::getToken($token);

        if (!$t) {
            return null;
        } else {
            $t->touch();
            $user = static::findOne($t->user_id);
            if ($user) {
                $user->accessToken = $token;
                $user->accessTokenExpires = $token;
            }
           

            return $user;  
        }
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
        if (!$this->accessToken) {
            $t = AuthToken::issueToken($this);
            $this->accessToken = $t->key;
            $this->accessTokenExpires = $t->time_expire;

            // Important!
            // Cleanup temporary here
            if (rand(0, 100) == 1) $t->flush();
        }

        return $this->accessToken;
    }

    public function getAuthKeyExpirationTime() {
        return $this->accessTokenExpires;
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
        $this->auth_key = Helpers::randomString(32);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->recovery_code = Helpers::randomString(32) . '_' . time();
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