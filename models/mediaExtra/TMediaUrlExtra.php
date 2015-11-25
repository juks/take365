<?php

namespace app\models\mediaExtra;

use Yii;
use app\models\Media;
use app\components\Helpers;

trait TMediaUrlExtra {
    /**
     * Returns url to the image resource
     * @return string
     */
    public function getUrl() {
        $baseUrl = Helpers::getParam('mediaHost');

        return $baseUrl . Helpers::getParam('mediaBaseUrl') . '/' . $this->path . '/' . $this->filename . '.' . $this->ext;
    }

    /**
     * Returns url to the thumb resource
     * @return string
     */
    public function getThumbUrl($dimemsions) {
        $baseUrl = Helpers::getParam('mediaHost');

        return $baseUrl .  Helpers::getParam('mediaBaseUrl') . $this->getThumbPath($dimemsions, false);
    }

}