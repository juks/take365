<?php

use yii\db\Schema;
use yii\db\Migration;

class m160610_113226_like extends Migration
{

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp() {
        $this->execute("CREATE TABLE `like` (
          `target_id` int(10) unsigned NOT NULL,
          `target_type` int(10) unsigned NOT NULL,
          `created_by` int(10) unsigned NOT NULL,
          `time_created` int(10) unsigned NOT NULL,
          `is_active` tinyint(3) unsigned NOT NULL DEFAULT '0',
          UNIQUE KEY `uni` (`target_id`,`target_type`,`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->execute("ALTER TABLE `media` ADD `likes_count` INT(10) unsigned NOT NULL DEFAULT 0");
    }

    public function safeDown() {
        $this->execute("DROP TABLE `like`");
        $this->execute("ALTER TABLE `media` DROP `likes_count`");
    }
}
