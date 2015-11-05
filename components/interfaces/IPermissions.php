<?php

namespace app\components\interfaces;

/**
 * Methods used for permissions check
 */
interface IPermissions{
	function getIsPublic();
    function getCreatorIdField();
}