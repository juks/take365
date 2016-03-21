<?php

use yii\db\Schema;
use yii\db\Migration;

class m160303_165401_comment extends Migration {
    public function safeUp() {
        $this->execute("CREATE TABLE `comment` (`id` int(11) NOT NULL AUTO_INCREMENT,
                          `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
                          `parent_id` int(11) NOT NULL DEFAULT '0',
                          `lk` int(11) NOT NULL DEFAULT '0',
                          `rk` int(11) NOT NULL DEFAULT '0',
                          `level` int(11) NOT NULL DEFAULT '0',
                          `thread` int(11) DEFAULT '0',
                          `created_by` int(10) unsigned NOT NULL,
                          `target_type` tinyint(3) unsigned NOT NULL,
                          `target_id` int(11) NOT NULL DEFAULT '0' COMMENT 'linked object id',
                          `body` text,
                          `body_jvx` text,
                          `time_created` int(10) unsigned NOT NULL,
                          `time_updated` int(10) unsigned NOT NULL,
                          PRIMARY KEY (`id`),
                          KEY `target_id_target_type_lk` (`target_type`,`target_id`,`lk`),
                          KEY `search` (`time_updated`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("ALTER TABLE story ADD comments_count int unsigned not null AFTER time_published");
    }

    public function safeDown() {
        $this->execute('DROP TABLE `comment`');
        $this->execute('ALTER TABLE story DROP COLUMN comments_count');
    }
}
