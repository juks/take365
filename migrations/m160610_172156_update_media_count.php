<?php

use yii\db\Schema;
use yii\db\Migration;

class m160610_172156_update_media_count extends Migration
{
    public function up() {
        $this->execute('UPDATE story s SET media_count = (SELECT count(id) FROM media m WHERE target_id = s.id AND m.target_type = 2 AND m.is_deleted = 0)');
    }

    public function down() {
        echo "m160610_172156_update_media_count cannot be reverted.\n";

        return false;
    }

}
