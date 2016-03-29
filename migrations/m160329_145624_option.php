<?php

use yii\db\Schema;
use yii\db\Migration;

class m160329_145624_option extends Migration {
    public function safeUp() {
        $this->execute(
                    "CREATE TABLE `option` (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `name` varchar(128) DEFAULT NULL,
                      `default` varchar(255) DEFAULT NULL,
                      `group_id` smallint(5) unsigned NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `name` (`name`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
                    );

        $this->execute("INSERT INTO `option` (`name`, `default`, `group_id`) VALUES ('notify', 'true', 1)");

        $this->execute(
                    "CREATE TABLE `option_value` (
                      `target_id` int(10) unsigned NOT NULL,
                      `target_type` smallint(5) unsigned NOT NULL,
                      `option_id` int(10) unsigned NOT NULL,
                      `type` tinyint(3) unsigned NOT NULL,
                      `value_storable` varchar(255) DEFAULT NULL,
                      UNIQUE KEY `uni` (`target_id`,`target_type`,`option_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
                    );
    }

    public function safeDown() {
        $this->execute("DROP TABLE `option`");
        $this->execute("DROP TABLE `option_value`");
    }
}
