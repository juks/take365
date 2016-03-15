<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Model;

class ApiCommentForm extends Model {
    public $id;
    public $targetType;
    public $targetId;
    public $body;
    public $parentId;
    public $commentId;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['body'], 'required'],
            [['id', 'targetId', 'targetType', 'parentId'], 'integer'],
            [['targetId', 'targetType'], 'required', 'when' => function($m) { return !$m->id; }],
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'id' => 'id',
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
