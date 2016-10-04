<?php

use yii\db\Schema;
use yii\db\Migration;

class m161004_131324_posts extends Migration
{
    public function safeUp() {
        $this->execute('CREATE TABLE `blog` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `created_by` int(10) unsigned DEFAULT \'0\',
                          `time_created` int(10) unsigned DEFAULT \'0\',
                          `time_updated` int(10) unsigned DEFAULT \'0\',
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->execute('INSERT INTO blog (created_by, time_created) values (1, unix_timestamp())');

        $this->execute('CREATE TABLE `post` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `created_by` int(10) unsigned DEFAULT \'0\',
                          `time_created` int(10) unsigned DEFAULT \'0\',
                          `time_updated` int(10) unsigned DEFAULT \'0\',
                          `time_published` int(10) unsigned DEFAULT \'0\',
                          `blog_id` int(10) unsigned DEFAULT \'0\',
                          `is_published` tinyint(3) unsigned DEFAULT \'0\',
                          `title` varchar(255) DEFAULT NULL,
                          `body` text,
                          `body_jvx` text,
                          PRIMARY KEY (`id`),
                          KEY `list` (`blog_id`,`is_published`,`time_published`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }

    public function safeDown() {
        $this->execute('DROP TABLE `blog`');
        $this->execute('DROP TABLE `post`');
    }
}
