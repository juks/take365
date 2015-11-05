<?php

namespace app\components;

class BlitzTemplate {
    function init() {

    }

    function render($context, $file, $data = [], $return = false) {
        $tpl = new \Blitz($file);

        if ($return) {
            return $tpl->parse($data);
        } else {
            echo $tpl->parse($data);
        }
    }
}