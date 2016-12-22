<?php

namespace app\models\base;

use Yii;
use app\components\Ml;

/**
 * This is the model class for table "auth_user".
 *
 * @property integer $id
 * @property integer $user_type
 * @property string $username
 * @property string $password
 * @property integer $time_created
 * @property integer $time_updated
 * @property integer $ip_created
 * @property integer $time_login
 * @property integer $ip_login
 * @property integer $is_active
 * @property integer $is_banned
 * @property integer $failure_counter
 * @property string $email
 * @property integer $email_confirmed
 * @property string $homepage
 * @property string $fullname
 * @property integer $sex
 * @property string $timezone
 * @property string $description
 * @property string $description_jvx
 * @property string $recovery_code
 * @property integer $recovery_code_time_issued
 * @property string $option_code
 */
class AuthUserBase extends \yii\db\ActiveRecord
{
    protected $_reservedNames = ['me', 'friends', 'friend', 'take365', 'root', 'admin', 'superuser'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_user';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_type', 'ext_type', 'time_created', 'time_updated', 'time_registered', 'ip_created', 'time_login', 'ip_login', 'is_active', 'is_banned', 'failure_counter', 'email_confirmed', 'sex', 'recovery_code_time_issued'], 'integer'],
            [['time_created', 'ip_created'], 'required'],
            ['password', 'required', 'when' => function($m) { return !$m->ext_type; }],
            ['email', 'required', 'when' => function($m) { return !$m->ext_type; }],
            ['username', 'string', 'min' => 1, 'max' => 20],
            [['password', 'fullname'], 'string', 'min' => 6, 'max' => 64],
            ['timezone', 'string', 'max' => 32],
            [['username', 'email'], 'trim'],
            ['username', 'checkUsernameReserved'],
            ['username', 'checkUsernameValid'],
            ['username', 'checkUsernameExists'],
            ['password', 'checkPasswordWeak'],
            ['email', 'email'],
            ['email', 'checkEmailExists'],
            [['description', 'description_jvx'], 'string', 'max' => 1024],
            ['recovery_code', 'string', 'max' => 16],
            ['option_code', 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_type' => 'User Type',
            'username' => 'Username',
            'password' => 'Password',
            'time_created' => 'Time Created',
            'time_updated' => 'Time Updated',
            'time_registered' => 'Time Registered',
            'ip_created' => 'Ip Created',
            'time_login' => 'Time Login',
            'ip_login' => 'Ip Login',
            'is_active' => 'Active',
            'is_banned' => 'is_banned',
            'failure_counter' => 'Failure Counter',
            'email' => 'Email',
            'email_confirmed' => 'Email Confirmed',
            'homepage' => 'Homepage',
            'fullname' => 'Fullname',
            'sex' => 'Sex',
            'description' => 'Description',
            'description_jvx' => 'Description Jevix',
            'recovery_code' => 'Recovery Code',
            'recovery_code_time_issued' => 'Recovery Code Time Issued',
            'option_code' => 'Option Code'
        ];
    }

    /*
    *   Check if username is not reserved
    */
    public function checkUsernameReserved($attribute, $params) {
        if (array_search(strtolower($this->$attribute), $this->_reservedNames) != null) {
            $this->addError($attribute, Ml::t('Sorry, username ' . $this->$attribute . ' is reserved'));
        }
    }

    public function checkPasswordWeak($attribute, $params) {
        if (preg_match('/^(123456|qwertyasdf|12345678|654321|87654321)$/i', $this->$attribute)) {
            $this->addError($attribute, Ml::t('Given password is insecure'));
        }

        if ($this->username && $this->$attribute == $this->username) {
            $this->addError($attribute, Ml::t('Given password is insecure'));
        }

        if ($this->email && $this->$attribute == $this->email) {
            $this->addError($attribute, Ml::t('Given password is insecure'));
        }
    }

    /*
    *   Check if username contains only valid symbols
    */
    public function checkUsernameValid($attribute, $params) {
        if ($this->scenario != 'import' && !self::isValidUsername($this->$attribute)) $this->addError($attribute, Ml::t('Invalid username'));
    }

    public static function isValidUsername($username) {
        return preg_match('/^[a-z0-9!][a-z0-9\-._~\[\]@!$()*+=.]*$/i', $username);
    }

    public function checkUsernameExists($attribute) {
        $cond = ['username' => $this->$attribute, 'is_active' => 1];
        if ($this->id) $cond['id'] = ['!=', $this->id];

        if (self::find()->where(static::makeCondition($cond))->count()) $this->addError($attribute, Ml::t('This username has been already taken'));
    }

    public function checkEmailExists($attribute) {
        $cond = ['email' => $this->$attribute, 'is_active' => 1];
        if ($this->id) $cond['id'] = ['!=', $this->id];

        if (self::find()->where(static::makeCondition($cond))->count()) $this->addError($attribute, Ml::t('This email has been already taken'));
    }
}
