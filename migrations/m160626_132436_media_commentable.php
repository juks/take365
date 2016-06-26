<?php

use yii\db\Schema;
use yii\db\Migration;

class m160626_132436_media_commentable extends Migration
{
    public function safeUp() {
        $this->execute("ALTER TABLE media ADD comments_count INT UNSIGNED NOT NULL DEFAULT 0");
    }

    public function safeDown() {
        $this->execute("ALTER TABLE media DROP comments_count");
    }
}
