<?php

namespace app\models;

use Yii;
use app\models\MQueue;
use app\models\Storage;
use app\components\Helpers;
use app\models\base\MQueueAttachBase;
use app\components\traits\TModelExtra;

/**
 * MQueue attachment class
 */
class MQueueAttach extends MQueueAttachBase {
    use TModelExtra;

    /**
     *   Sets the attachment model scenarios
     **/
    public function scenarios() {
        return [
            'default' => ['message_id', 'attach_id']
        ];
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
        }

        return parent::beforeValidate();
    }

    public function getResource() {
        return $this->hasOne(Storage::className(), ['id' => 'attach_id'])->where(['is_deleted' => 0]);
    }

    /**
     * Deattach this item from message
     */
    public function deattach() {
        $parent = MQueue::findOne($this->message_id);

        if (!$parent) throw new \Exception('Failed to deattach item due to no target message found');

        Helpers::transact(function() use ($parent) {
            if ($this->delete()) {
                $parent->attach_count--;
                $parent->save();
            }
        });
    }
}