<?php

use yii\db\Schema;
use yii\db\Migration;

class m161130_155132_opt_newsletter extends Migration
{
    public function safeUp()
    {
        $this->execute("INSERT INTO `option` (`name`, `default`, `group_id`) VALUES ('newsletter', 'true',1)");
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM `option` WHERE `name`='newsletter'");
    }
}
