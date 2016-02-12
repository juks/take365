<?php

namespace app\models;

use app\models\base\AuthUserBase;
use yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\components\Helpers;
use app\components\HelpersTxt;
use app\components\Ml;
use app\components\traits\TCheckField;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;
use app\components\interfaces\IPermissions;
use app\components\interfaces\IGetType;
use app\models\AuthToken;
use app\models\Media;
use app\models\mediaExtra\MediaCore;
use app\models\mediaExtra\TMediaUploadExtra;
use app\models\Story;
use app\models\MQueue;


class User extends AuthUserBase implements IdentityInterface, IPermissions, IGetType {
    use TCheckField;
    use THasPermission;
    use TMediaUploadExtra;
    use TModelExtra;

    public $accessToken;
    public $accessTokenExpires;

    public $userpic;
    public $sexTitle;

    const typeId = 1;

    /**
    *   Sets the User model scenarios
    **/    
    public function scenarios() {
        return [
            'import' => ['id_old', 'username', 'password', 'email', 'description', 'description_jvx', 'is_active', 'ip_created', 'time_created', 'time_registered', 'sex', 'fullname', 'description']
        ];
    }

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
     * Returns integer type ID for this entity
     */
    public function getType() {
        return self::typeId;
    }

    /**
     * Returns the active user's criteria
     */
    public static function getActiveCondition() {
        return ['is_active' => 1];
    }

    /**
     * Returns user entity only if it is active
     */
    public static function getActiveUser($identity) {
        $conditions = [];

        if (is_int($identity)) {
            $conditions['id'] = $identity;
        } elseif (Helpers::checkEmail($identity)) {
            $conditions['email'] = $identity;
        } else {
            $conditions['username'] = $identity;
        }

        return self::find()->where(array_merge($conditions, self::getActiveCondition()))->one();
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
        return self::getActiveUser($username);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token, $id = null) {


        $user = static::findOne([
            'recovery_code' => $token
        ]);

        if ($id && $user && $user->id != $id) {
            throw new \app\components\ModelException("Invalid security code!");
        }

        if ($user && (time() - $user->recovery_code_time_issued > Helpers::getParam('user/recoveryLifetime'))) {
            throw new \app\components\ModelException("Security token is expired!");
        }
        
        return $user;
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
        $t = AuthToken::getToken($authKey);

        return $t ? true : false;
        //return $this->getAuthKey() === $authKey;
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
        if ($this->scenario != 'import')
            $this->password = md5($password);
        else
            $this->password = $password;
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
        if (time() - $this->recovery_code_time_issued < 60) throw new \app\components\ControllerException(Ml::t('Too many recovery attempts. Try again in few moments'));

        $this->recovery_code = Helpers::randomString(16);
        $this->recovery_code_time_issued = time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->recovery_code_time_issued = null;
        $this->recovery_code = null;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            $this->time_created = time();
            if (!$this->ip_created) $this->ip_created = ip2long(Yii::$app->request->userIP);
            $this->generatePasswordResetToken();
        } else {
            $this->time_updated = time();
        }

        if (!$this->_oldAttributes['description'] !== $this->description) $this->description_jvx = HelpersTxt::simpleText($this->description);

        return parent::beforeValidate();
    }

    /**
     * Register new user
     */
    public function register() {
        MQueue::compose()
                        ->to($this->email)
                        ->subject('Регистрация')
                        ->bodyTemplate('registrationConfirm.php', ['confirmUrl' => $this->urlConfirm])
                        ->send();
    }

    /**
     * Recover User password
     */
    public function recover() {
        $this->generatePasswordResetToken();
        $this->save();
        
        MQueue::compose()
                        ->to($this->email)
                        ->subject('Изменение пароля')
                        ->bodyTemplate('recoverConfirm.php', ['confirmUrl' => $this->urlRecoverConfirm, 'username' => $this->fullNameFilled])
                        ->send();
    }

    /**
     * Update Recovery Password
     */
    public function recoverUpdate($password) {
        $this->removePasswordResetToken();
        $this->password = $password;
        $this->save();

        MQueue::compose()
                        ->to($this->email)
                        ->subject('Изменение пароля')
                        ->bodyTemplate('recoverNotice.php', ['confirmUrl' => $this->urlRecoverConfirm, 'username' => $this->fullNameFilled, 'ip' => Yii::$app->request->userIP])
                        ->send();
    }

    /**
     * Before save event
     * @param  array $insert
     */
    public function beforeSave($insert) {
        if(isset($this->password)) $this->setPassword($this->password);

        return parent::beforeSave($insert);
    }

    /**
     * Userpic relation
     */
    public function getUserpic() {
       $mo = Media::getMediaOptions('userpic');

       return $this->hasOne(Media::className(), ['target_id' => 'id', 'target_type' => 'type'])->where(['type' => $mo[MediaCore::typeId], 'is_deleted' => 0])->one();
    }

    /**
     * Stories relation
     */
    public function getStories() {
        if ($this->hasPermission(Yii::$app->user, IPermissions::permWrite)) {
            $conditions = [];
            $order = 'time_published DESC, status';
        } else {
            $conditions = ['status' => Story::statusPublic];
            $order = 'time_published DESC';
        }

        return $this->hasMany(Story::className(), ['created_by' => 'id'])->where($conditions)->orderBy($order);
    }

    public function format($xtra = []) {
        $this->getImages();
        $this->sex = intval($this->sex);

        $this->sexTitle = ['undefined', 'male', 'female'][$this->sex];
    }

    public function getImages($extra = []) {
        $this->userpic = $this->getUserpic();
    }

    public function getFullnameFilled() {
        return $this->fullname ? $this->fullname : $this->username;
    }

    /**
    * Checks if this user is equal to current user
    */
    public function getThisIsMe() {
        return Yii::$app->user->id == $this->id;
    }

    /**
    * Forms user home URL
    */
    public function getUrl() {
        return \yii\helpers\Url::base(true) . '/' . $this->username;
    }

    /**
    * Forms user profile URL
    */
    public function getUrlProfile() {
        return \yii\helpers\Url::base(true) . '/' . $this->username . '/profile';
    }

    /**
    * Forms user profile update URL
    */
    public function getUrlEdit() {
        return \yii\helpers\Url::base(true) . '/' . $this->username . '/profile/edit/';
    }

    /**
    * Forms user register confirm url
    */
    public function getUrlConfirm() {
        return \yii\helpers\Url::base(true) . '/register/confirm?id=' . $this->id . '&code=' . $this->recovery_code;
    }

    /**
    * Forms user recover confirm url
    */
    public function getUrlRecoverConfirm() {
        return \yii\helpers\Url::base(true) . '/register/recover?id=' . $this->id . '&code=' . $this->recovery_code;
    }

    /**
    * Checks if user owns any stories
    */
    public function getHasStories() {
        return $this->getStories()->count() > 0;
    }
}

?>