<?php

namespace app\components;

use app\components\BlitzTemplate;

class MyRenderer {
    protected $_rendereres = [];

    function render($context, $file, $data = [], $return = true) {
        if (empty($this->_rendereres[$file])) {
            $this->_renderers[$file] = new BlitzTemplate($file);
            $this->_renderers[$file]->setMethodHandler($context, $this->_renderers[$file]);
        }

        if ($return) {
            return $this->_renderers[$file]->parse($data);
        } else {
            echo $this->_renderers[$file]->parse($data);
        }
    }
}