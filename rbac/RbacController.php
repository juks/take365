<?php

use \app\rbac\UserProfileOwnerRule;
 
// add the rule
$userProfileOwnerRule = new UserProfileOwnerRule();
$authManager->add($userProfileOwnerRule);
 
$updateOwnProfile = $authManager->createPermission('updateOwnProfile');
$updateOwnProfile->ruleName = $userProfileOwnerRule->name;
$authManager->add($updateOwnProfile);
 
$authManager->addChild($brand, $updateOwnProfile);
$authManager->addChild($talent, $updateOwnProfile);

?>