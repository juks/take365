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
use app\components\traits\TOptionValue;
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
    use TOptionValue;

    public $accessToken;
    public $accessTokenExpires;

    public $userpicCache;
    public $sexTitle;

    const sexMale = 1;
    const sexFemale =2;
    const typeId = 1;

    const extAuthFacebook = 'facebook';
    const extAuthTwitter = 'twitter';
    const extAuthVKontatke = 'vkontakte';

    const defaultTimezone = 'Europe/Moscow';

    protected static $_extAuthServiceId = [
                                                self::extAuthFacebook => 1,
                                                self::extAuthTwitter => 2,
                                                self::extAuthVKontatke => 3,
                                          ];


    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        $f = [
            'id'            => 'id',
            'username'      => 'fullnameFilled',
            'url'           => 'url',
            'userpic'       => function() { $up = $this->userpic; return $up ? $up->getThumbData(Media::resizeMaxSide, 100) : null; },
            'userpicLarge'  => function() { $up = $this->userpic; return $up ? $up->getThumbData(Media::resizeMaxSide, 200) : null; }
        ];

        if (array_key_exists('isSubscribed', $this->relatedRecords)) $f['isSubscribed'] = function() { return !empty($this->relatedRecords['isSubscribed']); };

        return $f;
    }

    /**
    *   Sets the User model scenarios
    **/    
    public function scenarios() {
        return [
            'import' => ['id_old', 'username', 'password', 'email', 'description', 'description_jvx', 'is_active', 'ip_created', 'time_created', 'time_registered', 'sex', 'fullname', 'description'],
            'default' => ['username', 'fullname', 'email', 'password', 'description', 'is_active', 'sex', 'timezone', 'ext_type', 'ext_id']
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
        return ['is_active' => 1, 'is_banned' => 0];
    }

    public function getDefaultTimezone() {
        return $this->timezone && $this->timezone != 'none' ? $this->timezone : self::defaultTimezone;
    }

    /**
     * Returns user entity only if it is active
     */
    public static function getActiveUser($identity) {
        $conditions = [];

        if (is_int($identity)) {
            $conditions['id'] = $identity;
        } elseif (substr($identity, 0, 1) == '@') {
            $userId = intval(substr($identity, 1, strlen($identity) - 1));
            $conditions['id'] = $userId;            
        } elseif (Helpers::checkEmail($identity)) {
            $conditions['email'] = $identity;
        } else {
            $conditions['username'] = $identity;
        }

        return self::find()->where(array_merge($conditions, self::getActiveCondition()))->one();
    }

    /**
     * List users sugges by given part of their username
     *
     * @param array $params
     * @return int $maxItems
     */
    public static function suggest($params, $maxItems = 10) {
        if (strlen($params['username']) < 2) return [];
        if (!self::isValidUsername($params['username'])) return [];

        $conditions = self::getActiveCondition();
        $conditions['username'] = ['LIKE', $params['username'] . '%'];

        if (empty($params['followFlag'])) {
            return self::find()->where(self::makeCondition($conditions))->orderBy('username')->limit($maxItems)->all();
        } else {
            $items = self::find()->where(self::makeCondition($conditions))->with('isSubscribed')->orderBy('username')->limit($maxItems)->all();

            foreach ($items as $item) {
                if ($item->isSubscribed) true;
            }

            return $items;
        }
    }

    /**
     * Subscription relation
     * @return $this|null
     */
    public function getIsSubscribed() {
        $user = Yii::$app->user;

        if ($user->isGuest) {
            return null;
        } else {
            return $this->hasOne(\app\models\Feed::className(), ['user_id' => 'id'])->where(['reader_id' => $user->id, 'is_active' => 1]);
        }
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
                $user->accessTokenExpires = $t->time_expire;
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
    public function getAuthKey($noCreate = false) {
        if (!$this->accessToken && !$noCreate) {
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
        if ($this->recovery_code_time_issued && time() - $this->recovery_code_time_issued < 60) throw new \app\components\ControllerException(Ml::t('Too many recovery attempts. Try again in few moments'));

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
            if (!$this->recovery_code) $this->generatePasswordResetToken(); 
        } else {
            $this->time_updated = time();
        }

        if (!$this->_oldAttributes['description'] !== $this->description) $this->description_jvx = HelpersTxt::simpleText($this->description);

        return parent::beforeValidate();
    }

    public static function extLogin($client) {
        $extServiceId = self::getExtServiceId($client->name);
        if (!$extServiceId) throw new \Exception(Ml::t("Unknown external service"));
        $userAttributes = $client->getUserAttributes();    
        $user = self::find()->where(['ext_type' => $extServiceId, 'ext_id' => $userAttributes['id']])->one();

        // Should register a new one
        if (!$user) {
            $user = new User();

            $email = !empty($userAttributes['email']) ? $userAttributes['email'] : null;

            $user->setAttributes([
                                        'ext_type'  => $extServiceId,
                                        'ext_id'    => $userAttributes['id'],
                                        'email'     => $email,
                                        'is_active' => true
                                ]);

            // Try fetching facebook user attributes
            // Facebook
            if ($client->name == self::extAuthFacebook) {
                if (!empty($userAttributes['name'])) $user->fullname = $userAttributes['name'];
            // Twitter
            } elseif ($client->name == self::extAuthTwitter) {
                if (!empty($userAttributes['name'])) $user->fullname = $userAttributes['name'];
                if (!empty($userAttributes['screen_name']) && !self::getActiveUser($userAttributes['screen_name'])) $user->username = $userAttributes['screen_name'];
            // VKontatke
            } elseif ($client->name == self::extAuthVKontatke) {
                $fullname = !empty($userAttributes['first_name']) ? $userAttributes['first_name'] : '';
                if (!empty($userAttributes['last_name'])) $fullname .= ' ' . $userAttributes['last_name'];
                $user->fullname = $fullname;
                if (!empty($userAttributes['nickname']) && !self::getActiveUser($userAttributes['nickname'])) $user->username = $userAttributes['nickname'];
            }

            if ($email && !$user->validate(['email'])) throw new \yii\web\ConflictHttpException(Ml::t('This email address is already taken'));
            
            $user->save();
            if ($user->hasErrors()) {
                throw new \yii\web\ServerErrorException(Ml::t('Failed to create new user'));
            }
        }

        return Yii::$app->user->login($user);  
    }

    public static function getExtServiceId($serviceName) {
        if (!empty(self::$_extAuthServiceId[$serviceName])) {
            return self::$_extAuthServiceId[$serviceName];
        } else {
            return null;
        }
    }

    /**
    * Confirm user's email
    */
    public function confirmEmail() {
        $this->email_confirmed = true;
        return $this->save();
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
        if(isset($this->password) && $this->_oldAttributes['password'] != $this->password) $this->setPassword($this->password);

        return parent::beforeSave($insert);
    }

    /**
     * Userpic relation
     */
    public function getUserpic() {
       $mo = Media::getMediaOptions('userpic');

       return $this->hasOne(Media::className(), ['target_id' => 'id', 'target_type' => 'type'])->where(['type' => $mo[MediaCore::mediaTypeId], 'is_deleted' => 0]); //->one();
    }

    /**
     * Stories relation
     */
    public function getStories() {
        if ($this->hasPermission(Yii::$app->user, IPermissions::permWrite)) {
            $conditions = [];
            $order = 'is_deleted, time_published DESC, status';
        } else {
            $conditions = ['status' => Story::statusPublic, 'is_deleted' => false];
            $order = 'time_published DESC';
        }

        return $this->hasMany(Story::className(), ['created_by' => 'id'])->where($conditions)->orderBy($order);
    }

    public function getNotifyStories($date) {
        return Story::getNotifyStories($this->id, $date);
    }

    /**
     * Format current user
     * @param array $xtra
     */
    public function format($xtra = []) {
        $this->getImages();
        $this->sex = intval($this->sex);

        $this->sexTitle = ['undefined', 'male', 'female'][$this->sex];
    }

    /**
     * Retireves images
     * @param array $extra
     * @return $this
     */
    public function getImages($extra = []) {
        if ($this->userpicCache === null) $this->userpicCache = $this->getUserpic();

        return $this->userpicCache;
    }

    /**
     * Formt the fullname field according to user properties
     * @return mixed|string
     */
    public function getFullnameFilled() {
        if ($this->fullname) {
            return $this->fullname;
        } elseif ($this->usernameFilled) {
            return $this->usernameFilled;
        } else {
            return 'Пользователь';
        }
    }

    /**
    * Checks if this user is equal to current user
    */
    public function getThisIsMe() {
        return Yii::$app->user->id == $this->id;
    }

    /**
    * Return users identifier. If no username - let it be '@id'
    **/
    public function getUsernameFilled() {
        return $this->username ? $this->username : '@' . $this->id;
    }

    /**
    * Forms user home URL
    */
    public function getUrl() {
        return \yii\helpers\Url::base(true) . '/' . $this->getUsernameFilled();
    }

    /**
    * Forms user profile URL
    */
    public function getUrlProfile() {
        return \yii\helpers\Url::base(true) . '/' . $this->getUsernameFilled() . '/profile';
    }

    /**
    * Forms user profile update URL
    */
    public function getUrlEdit() {
        return \yii\helpers\Url::base(true) . '/' . $this->getUsernameFilled() . '/profile/edit/';
    }

    /**
     * Forms user profile URL
     */
    public function getUrlFeed() {
        return \yii\helpers\Url::base(true) . '/' . $this->getUsernameFilled() . '/feed';
    }

    /**
    * Forms user register confirm url
    */
    public function getUrlConfirm() {
        if (!$this->recovery_code || !$this->email) throw new \Exception('Some user data is missing');

        return \yii\helpers\Url::base(true) . '/register/confirm?id=' . $this->id . '&email=' . $this->email . '&code=' . $this->recovery_code;
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