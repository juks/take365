<?php

namespace app\models\mediaExtra;

use Yii;
use app\models\Media;

trait TMediaImageExtra {
    /**
     * Serializes and stores exif data
     * @param $exifData
     */
    public function setExifData($exifData) {
        $this->exif = serialize($exifData);
    }

    /**
     * Returns exif data stored inside object or reads it from image file
     * @return array|mixed
     */
    public function getExifData() {
        if ($this->exif) {
            return unserialize($this->exif);
        } elseif($this->_imageResource) {
            $exifData = $this->_imageResource->getImageProperties("exif:*");
            $this->setExifData($exifData);

            return $exifData;
        }
    }

    /**
     * Automatic image reorientation, based on exif data
     */
    function autoOrient() {
        $exifData = $this->getExifData();

        if (empty($exifData['exif:Orientation'])) return;

        $res = $this->getImageResource();
        $rotated = false;

        switch($exifData['exif:Orientation']) {
            case 3:
                $res->rotateImage(new ImagickPixel('none'), 180);
                $rotated = true;
                break;

            case 6:
                $res->rotateImage(new ImagickPixel('none'), 90);
                $width = $this->width; $this->width = $this->height; $this->height = $width;
                $rotated = true;
                break;

            case 8:
                $res->rotateImage(new ImagickPixel('none'), -90);
                $width = $this->width; $this->width = $this->height; $this->height = $width;
                $rotated = true;
                break;
        }

        if ($rotated) {
            $res->writeImage($this->_fullPath);
        }
    }
}