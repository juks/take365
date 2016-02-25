<?php

use yii\db\Schema;
use yii\db\Migration;

class m160225_120741_timezone extends Migration
{
    /*public function up()
    {

    }

    public function down()
    {
        echo "m160225_120741_timezone cannot be reverted.\n";

        return false;
    }*/

    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp() {
        $this->execute('alter table auth_user add timezone varchar(32) after sex');
    }

    public function safeDown() {
        $this->execute('alter table auth_user drop column timezone');
    }
}
