<?php

use yii\db\Schema;
use yii\db\Migration;

class m161201_151837_storage extends Migration
{
    public function safeUp()
    {
        $this->execute('CREATE TABLE `storage` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `created_by` int(10) unsigned NOT NULL,
          `time_created` int(10) unsigned NOT NULL,
          `time_updated` int(10) unsigned NOT NULL,
          `time_delete` int(10) unsigned NOT NULL,
          `filename` varchar(128) DEFAULT NULL,
          `ext` varchar(5) DEFAULT NULL,
          `mime` varchar(50) DEFAULT NULL,
          `size` int(10) unsigned NOT NULL,
          `partition` varchar(25) DEFAULT NULL,
          `path` varchar(255) DEFAULT NULL,
          `is_deleted` tinyint(3) unsigned NOT NULL,
          `key` varchar(255),
          PRIMARY KEY (`id`),
          KEY `created_by` (`created_by`),
          KEY `is_deleted` (`is_deleted`),
          KEY `time_delete` (`time_delete`),
          KEY `key` (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE `storage`');
    }
}