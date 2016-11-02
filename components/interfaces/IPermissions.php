<?php

namespace app\components\interfaces;

/**
 * Methods used for permissions check
 */
interface IPermissions {
	const permRead 		    = 1;
    const permWrite 	    = 2;
    const permAdmin 	    = 3;
    const permComment 	    = 4;
    const permLike          = 5;
    const permDeleteRecover = 6;

	function getIsPublic();
    function getCreatorIdField();
}