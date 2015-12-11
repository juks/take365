<?php

namespace app\components;

class BlitzTemplate extends \Blitz {
    protected $_methodHandler = false;
    protected $_renderer = false;

    function __call($method, $params = []) {
        array_unshift($params, $this->_renderer);

        if ($this->_methodHandler && method_exists($this->_methodHandler, $method)) {
            return call_user_func_array([$this->_methodHandler, $method], $params);
        } else {
            return 'Unknown Method';
        }
    }

    function setMethodHandler($handler, $renderer) {
        $this->_methodHandler = $handler;
        $this->_renderer = $renderer;
    }

    function render($context, $file, $data = [], $return = true) {
        $t = new \Blitz($file);
        //$this->load(file_get_contents($file));

        if ($return) {
            return $t->parse($data);
        } else {
            echo $t->parse($data);
        }
    }
}