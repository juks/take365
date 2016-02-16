<?php

use yii\db\Schema;
use yii\db\Migration;

class m160216_081148_ext_auth extends Migration
{
    public function up()
    {
        $this->execute('alter table auth_user add ext_id varchar(32) after id_old');
        $this->execute('alter table auth_user add ext_type int unsigned not null after id_old');
        $this->execute('alter table auth_user add index ext_id(ext_type, ext_id)');
        $this->execute('alter table auth_user drop index email');
        $this->execute('alter table auth_user add index email(email)');
        $this->execute('alter table auth_user change email email varchar(255) default \'\'');
    }

    public function down()
    {
        $this->execute('alter table auth_user drop ext_type');
        $this->execute('alter table auth_user drop ext_id');

    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
