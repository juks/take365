<?php

namespace app\components\traits;

/**
 * Trait implementing CheckField method
 */
trait TCheckField {
    /**
     * Проверяет валидность указанного поля для данной модели
     * @param string $fieldName
     * @param moxed $fieldvalue
     */
    public function checkField($fieldName, $fieldValue) {
        if (!$this->hasAttribute($fieldName)) throw new \Exception('Model ' . \yii\helpers\StringHelper::basename(get_class($this)) . ' has no field ' . $fieldName);
        $this->$fieldName = $fieldValue;
        $this->validate([$fieldName]);
    }
}

?>