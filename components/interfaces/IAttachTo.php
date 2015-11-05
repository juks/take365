<?php

namespace app\components\interfaces;

/**
 * Attach some entity to another by target type and id
 */
interface IAttachTo {
    function attachTo($entity);
}