<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Model;
use app\modules\api\models\ApiUser;
use app\components\Ml;

/**
 * LoginForm is the model behind the login form.
 */
class ApiLoginForm extends Model {
    public $username;
    public $password;
    public $rememberMe = true;
    public $token;
    public $tokenExpires;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'id'          => function($model) { return $this->_user->id; },
            'username'    => 'username',
            'token'       => function($model) { return $model->token; },
            'tokenExpires'=> function($model) { return $model->tokenExpires; }
        ];
    }


    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Ml::t('Incorrect username or password'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
        if (!$this->validate()) return false;
        
        $result = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);

        if ($result) {
            $this->token = $this->_user->getAuthKey();
            $this->tokenExpires = $this->_user->getAuthKeyExpirationTime();
        }

        return $result;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $this->_user = ApiUser::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Sets user object from outside
     *
     * @param $user
     */
    public function setUser($user) {
        $this->_user = $user;
        $this->username = $user->username;
        $this->token = $this->_user->getAuthKey();
        $this->tokenExpires = $this->_user->getAuthKeyExpirationTime();
    }
}
