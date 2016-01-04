<?php

namespace app\models\mediaExtra;

use Yii;
use add\models\media;

trait TMediaThumbExtra {
    /**
     * Returns the thumb data of the media resource if it is there
     * @param string $resizeMode
     * @param integer $dimension
     */
    public function getThumbData($resizeMode, $dimension, $extra = []) {
        $thumbsList = $this->getOption(self::thumbsList);

        if (empty($thumbsList[$resizeMode]) || array_search($dimension, $thumbsList[$resizeMode]) === false) return [];

        $t = [
                    'url'       => $this->t[$resizeMode][$dimension]['url'],
                    'width'     => $this->t[$resizeMode][$dimension]['width'],
                    'height'    => $this->t[$resizeMode][$dimension]['height'],
                ];

        if (!empty($extra['path'])) $t['path'] = $this->t[$resizeMode][$dimension]['path'];

        return $t;
    }

    /**
     * Run through the thumbs list and create thumb files
     * @param $extra
     */
    public function getThumbs($extra = null) {
        $this->t = ['id' => $this->id];

        $lastResized = [
        					'id' => $this->id,
        					'url' => $this->getUrl(),
        					'path' => $this->path,
        					'width' => $this->width,
        					'height' => $this->height,
        					'resized' => false
        				];

        $this->t['original'] = $lastResized;

        $thumbsList = $this->getOption(self::thumbsList);

        if (!$thumbsList) return;
        if (!is_array($thumbsList) || !count($thumbsList)) throw new \Exception('Wrong thumbs setting for media type ' . $this->type);

        foreach ($thumbsList as $resizeMode => $resizeDimensions) {
            foreach ($resizeDimensions as $targetDimension) {
                $thumb = $this->makeThumb($resizeMode, $targetDimension, $extra);

                if (empty($this->t[$resizeMode])) $this->t[$resizeMode] = [];
                
                if($thumb) {
                    $this->t[$resizeMode][$targetDimension]	= $thumb;
                    $lastResized = $thumb;
                } else {
                    $this->t[$resizeMode][$targetDimension]	= $lastResized;
                }
            }
        }
    }

    /**
     * Create thumb
     * @param $resizeMode
     * @param $targetDimension
     * @param $extra
     */
    public function makeThumb($resizeMode, $targetDimension, $extra = null) {
        $dimensions = $this->getThumbDimensions($resizeMode, $targetDimension);

        // Should we resize?
        // No, do not resize
        if (!$dimensions || $this->width <= $dimensions['width'] && $this->height <= $dimensions['height']) {
            return [
            			'id' 		=> $this->id,
            			'path' 		=> $this->path,
            			'width' 	=> $this->width,
            			'height' 	=> $this->height,
            			'url' 		=> $this->getUrl(),
            			'resized' 	=> false
            		];
        }

        // Yes, resize
        if ($dimensions) {
            $thumbPath = $this->getThumbPath($dimensions);

            if (!empty($extra[self::forceThumbsCreate]) || ((!empty($extra[self::thumbsCreate]) || $this->getParam('mediaThumbsAutoCreate')) && !file_exists($thumbPath))) {
                if (!$this->_thumbFolderReady) {
                    $this->preparePath('thumb');
                    $this->_thumbFolderReady = true;
                }

                $this->storeImageResource();
                $this->resize($dimensions, $thumbPath);
            }

            return [
            			'id' 		=> $this->id,
            			'path' 		=> $thumbPath,
            			'width' 	=> $dimensions['width'],
            			'height' 	=> $dimensions['height'],
            			'url' 		=> $this->getThumbUrl(['width' => $dimensions['width'], 'height' => $dimensions['height']]),
            			'resized' 	=> true
            		];
        }
    }

    /**
     * Get resulting thumb dimensions
     * @param $resizeMode
     * @param $targetDimension
     * @return array|null
     */
    public function getThumbDimensions($resizeMode, $targetDimension) {
        $cx = 0;
        $cy = 0;
        $cw = 0;
        $ch = 0;
        $crop = false;

        switch ($resizeMode) {
            case self::resizeWidth:
                $width = $this->width;
                $height = $this->height;

                $dimensionIndex = 0;

                break;

            case self::resizeHeight:
                $width = $this->width;
                $height = $this->height;

                $dimensionIndex = 1;

                break;

            case self::resizeMaxSide:
                $width = $this->width;
                $height = $this->height;

                $dimensionIndex = $this->width > $this->height ? 0 : 1;

                break;

            case self::resizeMinSide:
                $width = $this->width;
                $height = $this->height;

                $dimensionIndex = $this->width > $this->height ? 1 : 0;

                break;

            case self::resizeSquareCrop:
                if ($this->width > $this->height) {
                    $width = $this->height;
                    $height = $this->height;

                    $cx = round($this->width / 2 - $this->height / 2);
                    $cy = 0;
                    $cw = $width;
                    $ch = $height;
                } else {
                    $width = $this->width;
                    $height = $this->width;                    

                    $cx = 0;
                    $cy = round($this->height / 2 - $this->width / 2);
                    $cw = $width;
                    $ch = $height;
                }
            
                $dimensionIndex = 0;
                $crop = true;

                break;
        }

        $dimensionValue = $dimensionIndex ? $height : $width;
        $otherDimensionValue = $dimensionIndex ? $width : $height;

        if ($this->getOption(self::resizeScaleUp) || ($dimensionValue > $targetDimension)) {
            $proportion = $otherDimensionValue / $dimensionValue;

            if (!$dimensionIndex) {
                $newWidth  = $targetDimension;
                $newHeight = round($newWidth  * $proportion);
            } else {
                $newHeight = $targetDimension;
                $newWidth  = round($newHeight * $proportion);
            }

            return [
                        'width'     => $newWidth,
                        'height'    => $newHeight,
                        'cx'        => $cx,
                        'cy'        => $cy,
                        'cw'        => $cw,
                        'ch'        => $ch,
                        'crop'      => $crop
                    ];
        } else {
            return null;
        }
    }

    /**
     * Resize internal stored image into a file
     * @param $dimensions
     * @param $targetFile
     */
    public function resize($dimensions, $targetFile = null) {
        $image = $this->getImageResource();

        if (!$image) throw new Exception(Ml::t('Preloaded image resource is missing', 'media'));

        // IM
        if ($this->getOption(self::engine) == self::engineImageMagick) {
            $image = clone $image;

            // Maybe crop?
            if (!empty($dimensions['crop'])) {
                $image->cropImage($dimensions['cw'], $dimensions['ch'], $dimensions['cx'], $dimensions['cy']);
            }

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

            if (empty($dimensions['crop'])) {
                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $dimensions['width'], $dimensions['height'], $this->width, $this->height);
            } else {
                imagecopyresampled($newImage, $image, 0, 0, $dimensions['cx'], $dimensions['cy'], $dimensions['width'], $dimensions['height'], $this->width, $this->height);
            }

            // Save into file
            if ($targetFile) {
                // Need to remove the old file to avoid the size calculations errors
                if(is_file($targetFile)) unlink($targetFile);

                $savedImage = $this->saveGDImage($targetFile, $newImage);

                // If we were unable to save the result
                if(!$savedImage) throw new \Exception('Unable to save resized image');

                // Image destroy
                imagedestroy($newImage);
            // Return the result
            } else {
                return $newImage;
            }
        }
    }
}