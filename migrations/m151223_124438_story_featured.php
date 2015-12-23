<?php

use yii\db\Schema;
use yii\db\Migration;

class m151223_124438_story_featured extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE STORY ADD is_featured tinyint unsigned not null');
        $this->execute('ALTER TABLE STORY ADD index is_featured(is_featured)');
        $this->execute('UPDATE story SET is_featured = 1 WHERE id_old IN(28, 38, 42, 50, 115, 135, 159, 162, 183, 189, 194, 197, 203, 205, 240, 271, 273, 285, 295, 325, 336, 359, 389, 392, 416, 465, 481, 484, 485, 488, 490, 544, 551, 567) and status = 0');
    }

    public function down()
    {
        $this->execute('alter table story drop is_featured');
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
