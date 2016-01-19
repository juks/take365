<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Model;
use app\components\Ml;

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
            ['captcha', 'validateCaptcha', 'skipOnEmpty' => false],
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
        if (Yii::$app->request->isAjax) {
            if (!$this->$attribute)
                $this->addError($attribute, Ml::t('Please enter security code'));
            elseif (!\app\components\Captcha::validate($this->$attribute))
                $this->addError($attribute, Ml::t('Incorrect security code'));
        }
    }
}
