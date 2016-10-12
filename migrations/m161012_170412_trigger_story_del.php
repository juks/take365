<?php

use yii\db\Schema;
use yii\db\Migration;

class m161012_170412_trigger_story_del extends Migration
{
    public function safeUp() {
        $this->execute('DROP trigger story_status_update');
        $this->execute('CREATE TRIGGER story_status_update AFTER UPDATE ON story FOR EACH ROW BEGIN IF (new.status = 1 AND old.status = 0) OR (new.status = 0 AND new.is_deleted = 1 AND old.is_deleted = 0) THEN UPDATE media SET is_hidden = 1 WHERE target_id = new.id AND target_type = 2; ELSEIF (new.status = 0 AND old.status = 1) OR (new.status = 0 AND new.is_deleted = 0 AND old.is_deleted = 1) THEN UPDATE media SET is_hidden = 0 WHERE target_id = new.id AND target_type = 2; END IF; END;');
    }

    public function safeDown() {
        $this->execute('DROP trigger story_delete');
    }
}
