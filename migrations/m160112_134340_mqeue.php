<?php

use yii\db\Schema;
use yii\db\Migration;

class m160112_134340_mqeue extends Migration
{
    public function up()
    {
        $this->execute('CREATE TABLE `mqueue` (
                        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                        `time_created` int(10) unsigned NOT NULL,
                        `time_sent` int(10) unsigned NOT NULL,
                        `send_me` tinyint(3) unsigned NOT NULL,
                        `is_pending` tinyint(3) unsigned NOT NULL,
                        `pending_since` int(10) unsigned NOT NULL,
                        `to` varchar(255) DEFAULT NULL,
                        `headers` text,
                        `subject` varchar(255) DEFAULT NULL,
                        `body` text,
                        PRIMARY KEY (`id`),
                        KEY `pending` (`is_pending`,`pending_since`),
                        KEY `time_created` (`time_created`),
                        KEY `send_me` (`send_me`,`is_pending`,`time_created`)
                    ) ENGINE=InnoDB');
    }

    public function down()
    {
        $this->execute('DROP TABLE `mqueue`');
    }
}
