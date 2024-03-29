<?php

namespace app\components\traits;

use app\components\interfaces\IPermissions;

/**
 * Trait implementing Permissions checks
 */
trait THasPermission {
    /**
     * Проверяет начилие прав на доступ пользователя к объекту
     * @param object $fieldName
     * @param int $fieldvalue
     */
    public function hasPermission($user, $permission = IPermissions::permRead) {
        if (method_exists($this, 'checkPermission')) return $this->checkPermission($user, $permission);

        $roles = \Yii::$app->authManager->getRolesByUser($user->id);
        if (!empty($roles['admin'])) return true;

        if (!method_exists($this, 'getCreatorIdField')) throw new \Exception("No getCreatorIdField() method defined for class " . get_class($this));
        if (!method_exists($this, 'getIsPublic')) throw new \Exception("No getIsPublic() method defined for class " . get_class($this));

        $creatorIdField = $this->getCreatorIdField();

        if (($permission == IPermissions::permRead || $permission == IPermissions::permLike) && $this->getIsPublic()) return true;
        if ($this->$creatorIdField == $user->id) return true;

        return false;
    }
}

?>