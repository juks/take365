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
 * @property integer $banned
 * @property integer $failure_counter
 * @property string $email
 * @property integer $email_confirmed
 * @property string $fullname
 * @property integer $sex
 * @property string $description
 * @property string $recovery_code
 * @property integer $recovery_code_time_issued
 * @property integer $invite_id
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
            [['user_type', 'time_created', 'time_updated', 'time_registered', 'ip_created', 'time_login', 'ip_login', 'is_active', 'banned', 'failure_counter', 'email_confirmed', 'sex', 'recovery_code_time_issued', 'invite_id'], 'integer'],
            [['username', 'email', 'password', 'time_created', 'ip_created'], 'required'],
            [['username'], 'string', 'min' => 1, 'max' => 20],
            [['password', 'fullname'], 'string', 'max' => 64],
            ['username', 'unique'],
            ['username', 'checkReserved'],
            ['username', 'checkValid'],
            [['email'], 'email'],
            [['email'], 'checkEmailExists'],
            [['description'], 'string', 'max' => 1024],
            [['recovery_code'], 'string', 'max' => 8],
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
            'banned' => 'Banned',
            'failure_counter' => 'Failure Counter',
            'email' => 'Email',
            'email_confirmed' => 'Email Confirmed',
            'fullname' => 'Fullname',
            'sex' => 'Sex',
            'description' => 'Description',
            'recovery_code' => 'Recovery Code',
            'recovery_code_time_issued' => 'Recovery Code Time Issued',
            'invite_id' => 'Invite ID',
        ];
    }

    /*
    *   Check if username is not reserved
    */
    public function checkReserved($attribute, $params) {
        if (array_search(strtolower($this->$attribute), $this->_reservedNames) != null) {
            $this->addError($attribute, Ml::t('Sorry, username ' . $this->$attribute . ' is reserved'));
        }
    }

    /*
    *   Check if username contains only valid symbols
    */
    public function checkValid($attribute, $params) {
        if ($this->scenario != 'import' && !preg_match('/^[a-z][a-z0-9-]{1,}$/i', $this->$attribute)) $this->addError($attribute, Ml::t('Invalid username'));
    }

    public function checkEmailExists($attribute, $params) {
        if (self::find()->where(['email' => $this->$attribute, 'is_active' => 1, 'id' => ['!=', $this->id]])->count()) $this->addError($attribute, Ml::t('This email has been already taken'));
    }
}
