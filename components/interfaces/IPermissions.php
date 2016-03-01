<?php

namespace app\components\interfaces;

/**
 * Methods used for permissions check
 */
interface IPermissions{
	const permRead = 1;
    const permWrite = 2;
    const permAdmin = 3;

	function getIsPublic();
    function getCreatorIdField();
}