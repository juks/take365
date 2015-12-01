<?php

namespace app\commands;

use yii;
use yii\console\Controller;
use app\models\User;

class ImportController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionUsers() {
		$rows = (new \yii\db\Query())
		    ->select('*')
		    ->from('auth_users');

		$b = $rows->batch(100);
		$b->db = \Yii::$app->db1;

		foreach ($b as $i => $batchData) {
        	foreach ($batchData as $userData) {
        		$user = new User();
        		$user->setScenario('import');

        		$user->setAttributes(
        			[
        				'username'			=> $userData['login'],
        				'email'				=> $userData['email'],
        				'ip_created'		=> $userData['ip_created'],
        				'password'			=> $userData['password'],
        			]
        		);

        		if (!$user->save()) {
        			echo "--- Failed to save user ---";
        			print_r($user->attributes);
        			print_r($user->getErrors());
        			die();
        		}
        	}
        }
    }
}
