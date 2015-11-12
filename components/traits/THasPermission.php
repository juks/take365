<?php

namespace app\components\traits;

use app\components\interfaces\IPermissions;

/**
 * Trait implementing CheckField method
 */
trait THasPermission {
    /**
     * Проверяет начилие прав на доступ пользователя к объекту
     * @param object $fieldName
     * @param int $fieldvalue
     */
    public function hasPermission($user, $permission = IPermissions::permRead) {
        $roles = \Yii::$app->authManager->getRolesByUser($user->id);
        $creatorIdField = $this->getCreatorIdField();

        if ($permission == IPermissions::permRead && $this->getIsPublic()) return true;
        if ($this->$creatorIdField == $user->id || !empty($roles['admin'])) return true;

        return false;
    }
}

?>