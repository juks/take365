<?php

use yii\db\Schema;
use yii\db\Migration;

class m161108_185201_fix_trigger extends Migration
{
    public function safeUp() {
        $this->execute('DROP trigger media_insert_hidden');
        $this->execute('CREATE TRIGGER media_insert_hidden BEFORE INSERT ON media FOR EACH ROW BEGIN IF new.target_type = 2 THEN SELECT `status`, `is_deleted` INTO @st, @deleted FROM story where id = NEW.target_id; IF (@st = 1 OR @deleted = 1) THEN SET NEW.is_hidden = 1; END IF; END IF; END;');
    }

    public function safeDown() {

    }
}
