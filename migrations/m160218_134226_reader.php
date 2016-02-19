<?php

use yii\db\Schema;
use yii\db\Migration;

class m160218_134226_reader extends Migration
{
    public function up()
    {
        $this->execute('create table feed (reader_id int unsigned not null, user_id int unsigned not null, time_created int unsigned not null, unique index uni (reader_id, user_id)) engine innodb default charset utf8;');
    }

    public function down()
    {
        $this->execute('drop table feed');
    }
}
