<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Model;

class ApiRecoverForm extends Model {
    public $email;
    public $captcha;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['email'], 'required'],
            ['email', 'email'],
            ['captcha', 'validateCaptcha'],
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'email'       => 'Email',
            'captcha'     => 'Captcha'
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
    public function validateCaptcha($attribute, $params) {
        if (Yii::$app->request->isAjax && empty($_SESSION['CAPTCHAString']) || $this->attribute != $_SESSION['CAPTCHAString']) {
            $this->addError($attribute, 'Incorrect security code');
        }
    }
}
