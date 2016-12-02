<?php

use yii\db\Schema;
use yii\db\Migration;

class m161202_081803_attach extends Migration
{
    public function safeUp()
    {
        $this->execute('CREATE TABLE `mqueue_attach` (
          `message_id` int(10) unsigned NOT NULL,
          `attach_id` int(10) unsigned NOT NULL,
          `name` varchar(255),
          `time_created` int(10) unsigned NOT NULL,
          UNIQUE KEY `uni` (`message_id`,`attach_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->execute('ALTER TABLE `mqueue` ADD `attach_count` tinyint(3) UNSIGNED NOT NULL');
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE `mqueue_attach`');
        $this->execute('ALTER TABLE `mqueue` DROP `attach_count`');
    }
}
