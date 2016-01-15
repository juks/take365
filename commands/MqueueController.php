<?php

namespace app\commands;

use app\components\MyJobController;
use app\models\MQueue;

class MqueueController extends MyJobController {
    public function jobProcess() {
    	MQueue::processQueue();

        if ($this->checkPeriod('m', 30)) MQueue::releasePending();
        if ($this->checkPeriod('h', 1)) {
            echo "Drop Oldies\n";
            MQueue::dropOldies();
        }
    }
}