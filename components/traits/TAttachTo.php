<?php

namespace app\components\traits;

/**
 * Trait implementing IAttachTo methods
 */
trait TAttachTo {
    /**
     * Attaches object to some other entity
     * @param $entity
     */
    function attachTo($entity) {
        $this->target_id = $entity->id;
        $this->target_type = $entity->getType();
    }
}

?>