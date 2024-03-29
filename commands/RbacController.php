<?php
namespace app\commands;
 
use Yii;
use yii\console\Controller;
use \app\rbac\UserGroupRule;
use \app\models\User;
 
class RbacController extends Controller
{
    public function actionAddRole() 
    {
	   $authManager = \Yii::$app->authManager;
       $r = $authManager->getRole('user');

       $authManager->assign($r, 1);
    }

    public function actionInit()
    {
        $authManager = \Yii::$app->authManager;
 
        // Create roles
        $guest  = $authManager->createRole('guest');
        $user  = $authManager->createRole('user');
        $admin  = $authManager->createRole('admin');
 
        // Create simple, based on action{$NAME} permissions
        $login  = $authManager->createPermission('login');
 
        // Add roles in Yii::$app->authManager
        $authManager->add($guest);
        $authManager->add($user);
        $authManager->add($admin);

        $logins = ['juks', 'lusever', 'oracle'];
        $users = User::find()->where(['username' => $logins])->all();

        foreach($users as $user) {
            $authManager->assign($admin, $user->id);
        }

    }
}