<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Model;

class ApiRecoverUpdateForm extends Model {
    public $id;
    public $code;
    public $password;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['id', 'code', 'password'], 'required'],

        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'id'       => 'User Id',
            'code'     => 'Security Code',
            'password'       => 'New Password'
        ];
    }

    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }
}
