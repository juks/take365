<?php

use yii\db\Schema;
use yii\db\Migration;

class m161025_130452_annotation extends Migration
{
    public function safeUp() {
        $this->execute('ALTER TABLE `media` ADD `is_annotated` tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->execute('CREATE TABLE `media_annotation` (
                            `media_id` int(10) unsigned NOT NULL,
                            `time_created` int(10) unsigned NOT NULL,
                            `time_updated` int(10) unsigned NOT NULL,
                            `data` text,
                            `extra` text,
                            UNIQUE KEY `media_id` (`media_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }

    public function safeDown() {
        $this->execute('ALTER TABLE `media` DROP `is_annotated`');
        $this->execute('DROP TABLE `media_annotation`');
    }
}
