<?php

namespace app\models\mediaExtra;

use Yii;
use add\models\media;

trait TMediaDeleteExtra {
    /**
     * Marks item for deletion
     */
    public function markDeleted() {
        $this->is_deleted = 1;
        $this->save();
    }

    /**
     * Deletes items marked for deletion
     * @static
     *
     */
    public static function deleteMarked($maxDelete = 0) {
        $cr = new CDbCriteria(['condition' =>['is_deleted' => 1]]);
        if ($maxDelete) $cr->limit = $maxDelete;

        $mediaList = Media::model()->findAllByAttributes($cr);

        foreach($mediaList as $item) {
            $item->delete();
        }
    }

    /**
     * Deletes media item and it's files
     */
    public function delete() {
        if (file_exists($this->_fullPath)) unlink($this->_fullPath);
        $this->deleteRecursive($this->_thumbFolder);
        $this->deleteRecursive($this->_storeFolder);

        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();

        try {
            //TextItem::model()->deleteTargetText($this);
            parent::delete();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $transaction->commit();
    }

    /**
     * Recursive folder deletion
     * @param $path
     * @return bool|null
     * @throws Exception
     */
    function deleteRecursive($path) {
        $this->deleteRecursive($path);
    }
}