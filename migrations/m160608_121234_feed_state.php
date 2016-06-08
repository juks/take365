<?php

use yii\db\Schema;
use yii\db\Migration;

class m160608_121234_feed_state extends Migration
{
    public function safeUp() {
        $this->execute("ALTER TABLE feed ADD COLUMN is_active TINYINT UNSIGNED NOT NULL DEFAULT 0");
        $this->execute("ALTER TABLE feed DROP INDEX uni");
        $this->execute("ALTER TABLE feed ADD UNIQUE INDEX uni (reader_id, is_active, user_id)");
    }

    public function safeDown() {
        $this->execute("ALTER TABLE feed DROP INDEX uni");
        $this->execute("ALTER TABLE feed DROP COLUMN is_active");
        $this->execute("ALTER TABLE feed ADD UNIQUE INDEX uni(reader_id, user_id)");
    }
}
