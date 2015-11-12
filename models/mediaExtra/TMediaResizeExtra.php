<?php

namespace app\models\mediaExtra;

use Yii;
use add\models\media;

trait TMediaResizeExtra {
    /**
     * Resize internal stored image into a file
     * @param $dimensions
     * @param $targetFile
     */
    public function resize($dimensions, $targetFile = null) {
        $image = $this->getImageResource();

        if (!$image) throw new Exception(Ml::t('Preloaded image resource is missing', 'media'));

        $image = clone $image;

        // IM
        if ($this->getOption(self::engine) == self::engineImageMagick) {
            $image->resizeImage($dimensions['width'], $dimensions['height'], $this->getOption(self::resizeFilter), $this->getOption(self::resizeBlur));

            // Save into file
            if ($targetFile) {
                // Need to remove the old file to avoid the size calculations errors
                if(is_file($targetFile)) unlink($targetFile);

                // Save the result image
                $image->setImageCompressionQuality($this->getOption(self::quality));

                if (!$image->writeImage($targetFile)) {
                    throw new Exception('Failed to write media file!');
                }
            // Return the result
            } else {
                return $image;
            }
        // GD
        } else {
            $newImage = imagecreatetruecolor($dimensions['width'], $dimensions['height']);

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $dimensions['width'], $dimensions['height'], $this->width, $this->height);

            // Save into file
            if ($targetFile) {
                // Need to remove the old file to avoid the size calculations errors
                if(is_file($targetFile)) unlink($targetFile);

                $savedImage = $this->saveGDImage($targetFile, $newImage);

                // If we were unable to save the result
                if(!$savedImage) throw new Exception($this->errorMsg[1006], 1006);

                // Image destroy
                // imagedestroy($newImage);
            // Return the result
            } else {
                return $newImage;
            }
        }
    }

    /**
     * Get resulting thumb dimensions
     * @param $resizeMode
     * @param $targetDimension
     * @return array|null
     */
    public function getThumbDimensions($resizeMode, $targetDimension) {
        switch ($resizeMode) {
            case self::resizeWidth:
            case self::resizeSquareCrop:
                $dimensionIndex = 0;

                break;
            case self::resizeHeight:
                $dimensionIndex = 1;

                break;
            case self::resizeMaxSide:
                $dimensionIndex = $this->width  > $this->height ? 0 : 1;

                break;
            case self::resizeMinSide:
                $dimensionIndex = $this->width  > $this->height ? 1 : 0;

                break;
        }

        $dimensionValue = $dimensionIndex ? $this->height : $this->width;
        $otherDimensionValue = $dimensionIndex ? $this->width : $this->height;

        if ($this->getOption(self::resizeScaleUp) || ($dimensionValue > $targetDimension)) {
            $proportion = $otherDimensionValue / $dimensionValue;

            if (!$dimensionIndex) {
                $newWidth  = $targetDimension;
                $newHeight = round($newWidth  * $proportion);
            } else {
                $newHeight = $targetDimension;
                $newWidth  = round($newHeight * $proportion);
            }

            return ['width' => $newWidth, 'height' => $newHeight];
        } else {
            return null;
        }
    }
}