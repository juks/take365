<?php

namespace app\components\traits;

/**
 * Trait implementing CheckField method
 */
trait THasPermission {
    /**
     * Проверяет начилие прав на доступ пользователя к объекту
     * @param object $fieldName
     * @param int $fieldvalue
     */
    public function hasPermission($user, $permission = 'read') {
        $roles = \Yii::$app->authManager->getRolesByUser($user->id);
        $creatorIdField = $this->getCreatorIdField();

        if ($permission == 'read' && $this->getIsPublic()) return true;
        if ($this->$creatorIdField == $user->id || !empty($roles['admin'])) return true;

        return false;
    }
}

?>