<?php

use yii\db\Schema;
use yii\db\Migration;

class m161108_115030_deletion extends Migration
{
    public function safeUp() {
        $this->execute('ALTER TABLE media ADD time_deleted int(10) unsigned NOT NULL DEFAULT 0 AFTER time_updated');
        $this->execute('UPDATE media SET time_deleted = unix_timestamp() where is_deleted = 1');
        $this->execute('ALTER TABLE media add index `purge` (is_deleted, time_deleted)');
        $this->execute('UPDATE story SET time_deleted = unix_timestamp() WHERE is_deleted = 1 AND time_deleted = 0');
    }

    public function safeDown() {
        $this->execute('ALTER TABLE media DROP time_deleted');
        $this->execute('ALTER TABLE media DROP index `purge`');
    }
}
