<?php

namespace app\components\interfaces;

/**
 * Attach some entity to another by target type and id
 */
interface IBelongsTo {
    function getBelongsToCondition($entity);
}