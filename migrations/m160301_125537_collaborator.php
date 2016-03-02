<?php

use yii\db\Schema;
use yii\db\Migration;

class m160301_125537_collaborator extends Migration
{
    public function safeUp() {
        $this->execute('CREATE TABLE story_collaborator (story_id int unsigned not null, user_id int unsigned not null, permission tinyint unsigned not null, is_confirmed tinyint unsigned not null, time_created int unsigned not null, index story_id(story_id), index user_id(user_id)) ENGINE innodb;');
        $this->execute('ALTER TABLE auth_user CHANGE banned is_banned tinyint unsigned not null;');
    }

    public function safeDown() {
        $this->execute('DROP TABLE story_collaborator');
        $this->execute('ALTER TABLE auth_user CHANGE banned banned tinyint unsigned not null;');
    }
}
