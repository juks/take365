<?php

namespace app\components\interfaces;

/**
 * Attach some entity to another by target type and id
 */
interface IBrothers {
    function getBrotherCondition($type = null);
}