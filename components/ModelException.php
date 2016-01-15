<?php

namespace app\components;

class ModelException extends \Exception
{
    protected $_fieldName;

    public function __construct($message = null, $fieldName = null, $code = 0)
    {
        $this->_fieldName = $fieldName;

        parent::__construct($message);
    }

    public function getFieldName() {
        return $this->_fieldName;
    }
}