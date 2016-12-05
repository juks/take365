<?php

use yii\db\Schema;
use yii\db\Migration;

class m161204_125736_mqueue_from extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE `mqueue` ADD `from` VARCHAR (255) AFTER `to`');
        $this->execute('ALTER TABLE `auth_user` ADD `option_code` VARCHAR(16)');
        $this->execute('UPDATE `auth_user` SET `option_code` = CONCAT(CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))),CHAR(FLOOR(65 + (RAND() * 25))))');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE `mqueue` DROP `from`');
        $this->execute('ALTER TABLE `auth_user` DROP `option_code`');
    }
}
