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
        $isChanged = false;

        //new ImagickPixel('none');

        switch ($exifData['exif:Orientation']) {
            case \imagick::ORIENTATION_TOPLEFT:
                break;
            case \imagick::ORIENTATION_TOPRIGHT:
                $res->flopImage();
                $isChanged = true;
                break;
            case \imagick::ORIENTATION_BOTTOMRIGHT:
                $res->rotateImage("#000", 180);
                $isChanged = true;
                break;
            case \imagick::ORIENTATION_BOTTOMLEFT:
                $res->flopImage();
                $res->rotateImage("#000", 180);
                $isChanged = true;
                break;
            case \imagick::ORIENTATION_LEFTTOP:
                $res->flopImage();
                $res->rotateImage("#000", -90);
                $isChanged = true;
                break;
            case \imagick::ORIENTATION_RIGHTTOP:
                $res->rotateImage("#000", 90);
                $isChanged = true;
                $width = $this->width; $this->width = $this->height; $this->height = $width;
                break;
            case \imagick::ORIENTATION_RIGHTBOTTOM:
                $res->flopImage();
                $res->rotateImage("#000", 90);
                $isChanged = true;
                $width = $this->width; $this->width = $this->height; $this->height = $width;
                break;
            case \imagick::ORIENTATION_LEFTBOTTOM:
                $res->rotateImage("#000", -90);
                $isChanged = true;
                $width = $this->width; $this->width = $this->height; $this->height = $width;
                break;
            default: // Invalid orientation
                break;
        }

        if ($isChanged) {
            $res->setImageOrientation(\imagick::ORIENTATION_TOPLEFT);
            $res->writeImage($this->_fullPath);
        }
    }
}