<?php

use yii\db\Schema;
use yii\db\Migration;

class m160219_163134_hidden_media extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE media ADD is_hidden tinyint(1) UNSIGNED NOT NULL AFTER is_deleted');
        //$this->execute('CREATE TRIGGER story_status_update AFTER UPDATE ON story FOR EACH ROW BEGIN IF new.status = 1 AND old.status = 0 THEN UPDATE media SET is_hidden = 1 WHERE target_id = new.id AND target_type = 2; ELSEIF new.status = 0 AND old.status = 1 THEN UPDATE media SET is_hidden = 0 WHERE target_id = new.id AND target_type = 2; END IF; END;');
        //$this->execute('CREATE TRIGGER media_insert_hidden BEFORE INSERT ON media FOR EACH ROW BEGIN IF new.target_type = 2 THEN SET @st = (SELECT `status` FROM story where id = NEW.target_id); IF (@st = 1) THEN SET NEW.is_hidden = 1; END IF; END IF; END;');
        $this->execute('UPDATE media SET is_hidden = 1 WHERE type = 2 AND target_type = 2 AND target_id IN (SELECT id FROM story WHERE status = 1)');
        $this->execute('ALTER TABLE media ADD INDEX feed(created_by, type, is_deleted, is_hidden, time_created)');

        $this->execute('ALTER TABLE media DROP id_old');
        $this->execute('ALTER TABLE story DROP id_old');
        $this->execute('ALTER TABLE auth_user DROP id_old');
    }

    public function down()
    {
        $this->execute('ALTER TABLE media DROP is_hidden');
        $this->execute('DROP trigger story_status_update');
        $this->execute('DROP trigger media_insert_hidden');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
