<?php

namespace app\components;

use yii\log\FileTarget;

class MyFileTarget extends FileTarget {
    const delimiter = "   \t";

    public function formatMessage($message)
    {
        return parent::formatMessage($message) . self::delimiter;
    }
}