<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class ApiMediaForm extends Model {
    public $targetId;
    public $targetType;
    public $mediaType;
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['targetId', 'targetType', 'mediaType'], 'required'],
            [['targetId', 'targetType'], 'integer'],
            [['file'], 'file', 'skipOnEmpty' => false]
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'id'       => function($model) { return $this->media->id; },
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
