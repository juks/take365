<?php

use yii\db\Schema;
use yii\db\Migration;

class m170103_143029_fullnameindex extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `auth_user` ADD INDEX `fullname` (`fullname`)');
    }

    public function down()
    {
        $this->execute('ALTER TABLE `auth_user` DROP INDEX `fullname` (`fullname`)');
    }
}
