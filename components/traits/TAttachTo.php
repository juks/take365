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
    	$idField = method_exists($entity, 'getIdField') ? $entity->getIdField() : 'id';
        $this->target_id    = $entity->$idField;
        $this->target_type  = $entity->getType();
    }
}

?>