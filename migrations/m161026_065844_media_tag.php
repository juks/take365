<?php

use yii\db\Schema;
use yii\db\Migration;

class m161026_065844_media_tag extends Migration
{
    public function safeUp() {
        $this->execute('CREATE TABLE `media_tag` (
                                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `time_created` int(10) unsigned NOT NULL,
                                     `count` int(10) unsigned NOT NULL DEFAULT 0,
                                     `name` varchar(255),
                                     PRIMARY KEY `id` (`id`),
                                     UNIQUE KEY `name` (`name`),
                                     KEY `count` (`count`)
                                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE `media_tag_link` (
                                     `tag_id` int(10) unsigned NOT NULL,
                                     `media_id` int(10) unsigned NOT NULL,
                                     `is_active` tinyint(1) unsigned NOT NULL DEFAULT 0,
                                     `time_published` int(10) unsigned NOT NULL DEFAULT 0,
                                     `match` int(10) unsigned NOT NULL DEFAULT 0,
                                     UNIQUE KEY `uni` (`tag_id`, `media_id`),
                                     KEY `link` (`tag_id`, `is_active`, `time_published`),
                                     KEY `link_match` (`tag_id`, `is_active`, `match` DESC, `time_published`)
                                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->execute('ALTER TABLE `media` ADD INDEX `scan` (`type`, `is_deleted`, `is_hidden`, `is_annotated`)');
    }

    public function safeDown() {
        $this->execute('DROP TABLE `media_tag`');
        $this->execute('DROP TABLE `media_tag_link`');
        $this->execute('ALTER TABLE `media` DROP INDEX `scan`');
    }

}
