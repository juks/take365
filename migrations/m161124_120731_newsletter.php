<?php

use yii\db\Schema;
use yii\db\Migration;

class m161124_120731_newsletter extends Migration {
    public function safeUp() {
        $this->execute("CREATE TABLE `newsletter` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `created_by` int(10) unsigned DEFAULT '0',   `time_created` int(10) unsigned DEFAULT '0',   `time_updated` int(10) unsigned DEFAULT '0',   `time_sent` int(10) unsigned not null DEFAULT '0',   `title` varchar(255),   `body` text,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function safeDown() {
        $this->execute("DROP TABLE `newsletter`");
    }
}
