<?php

use yii\db\Schema;
use yii\db\Migration;

class m160801_125807_story_complete extends Migration
{
    public function safeUp() {
        $this->execute('ALTER TABLE `story` ADD COLUMN `is_complete` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_featured`');
        $this->execute('UPDATE `story` SET `is_complete` = 1 WHERE `media_count` >= 365');
        $this->execute('ALTER TABLE `story` DROP INDEX `completion`');
        $this->execute('ALTER TABLE `story` ADD INDEX `completion` (`status`, `is_complete`, `time_published`)');
    }

    public function safeDown() {
        $this->execute('ALTER TABLE `story` DROP INDEX `status_is_complete`');
        $this->execute('ALTER TABLE `story` DROP COLUMN `is_complete`');
    }
}