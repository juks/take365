<?php

namespace app\models\mediaExtra;

use app\components\Helpers;
use Yii;
use add\models\media;

trait TMediaDeleteExtra {
    /**
     * Marks item for deletion
     * @param $replace was this file replaced by other
     */
    public function markDeleted($replace = false) {
        $this->is_deleted = 1;
        $this->time_deleted = time();

        Helpers::transact(function() use ($replace) {
            if ($this->save()) {
                if (method_exists($this, 'afterMediaDelete')) $this->afterMediaDelete($replace);
            }
        });
    }

    /**
     * Recovers item from deleetd state
     */
    public function recoverDeleted() {
        $this->is_deleted = 0;
        $this->time_deleted = 0;
        if ($this->save()) {
            if (method_exists($this, 'afterMediaRecover')) $this->afterMediaRecover();
        }
    }
    
    /**
     * Deletes items marked for deletion
     * @static
     *
     */
    public static function deleteMarked($maxDelete = 100) {
        $lifeTime = Helpers::getParam('media/deletedLifetime');
        if (!$lifeTime) throw new \Exception('No lifetime parameter for media deletion');

        $mediaList = self::find()->where(self::makeCondition([
                                                                'is_deleted'    => 1,
                                                                'time_deleted'  => ['<', time() - $lifeTime]]
                                                            ))->limit($maxDelete)->all();

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

        Helpers::transact(function() {
            $likesList = $this->listLikes();
            if ($likesList) {
                foreach($likesList as $like) {
                    $like->delete();
                }
            }
            parent::delete();
        });
    }
}