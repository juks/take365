<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    public function actionName() {
        //echo \app\components\HelpersName::parseName('софiя'); return;

        foreach (\app\models\User::find()->batch(100) as $items) {
            foreach ($items as $item) {
                if (!$item->fullname) continue;

                $name =  \app\components\HelpersName::parseName($item->fullname);

                if (!$name) continue;

                echo  str_pad($item->fullname, 20, ' ', STR_PAD_LEFT) . ': ' . $name . "\n";
            }
        }
    }
}
