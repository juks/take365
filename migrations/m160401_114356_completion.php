<?php

use yii\db\Schema;
use yii\db\Migration;

class m160401_114356_completion extends Migration
{
    public function safeUp() {
        $this->execute("UPDATE story SET media_count = (SELECT count(*) FROM media WHERE target_type = 2 AND target_id = story.id AND is_deleted = 0)");
        $this->execute("ALTER TABLE story ADD INDEX completion(status, media_count, time_created)");
    }

    public function safeDown() {
        $this->execute("ALTER TABLE story DROP INDEX completion");
    }
}
