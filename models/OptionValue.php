<?php

namespace app\models;

use Yii;
use app\models\base\OptionValueBase;

/**
 * Option Value class
 */
class OptionValue extends OptionValueBase {
    public $value;

    const typeInteger   = 1;
    const typeFloat     = 2;
    const typeArray     = 3;
    const typeString    = 4;
    
    public function afterFind() {
        if ($this->type == self::typeInteger) $this->value = intval($this->value_storable);
        elseif ($this->type == self::typeFloat) $this->value = floatval($this->value_storable);
        elseif ($this->type == self::typeArray) $this->value = unserialize($this->value_storable);
        elseif ($this->type == self::typeString) $this->value = $this->value_storable;
    }

    public function beforeSave($insert) {
        if (is_int($this->value)) {
            $this->type = self::typeInteger;
            $this->value_storable = $this->value;
        } elseif (is_float($this->value)) {
            $this->type = self::typeFloat;
            $this->value_storable = $this->value;
        } elseif (is_array($this->value)) {
            $this->type = self::typeArray;
            $this->value_storable = serialize($this->value);
        } elseif(is_string($this->value)) {
            $this->type = self::typeString;
            $this->value_storable = $this->value;
        }

        return parent::beforeSave($insert);
    }
}