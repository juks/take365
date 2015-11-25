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
    public function getThumbData($resizeMode, $dimension) {
        $thumbsList = $this->getOption(self::thumbsList);

        if (empty($thumbsList[$resizeMode]) || array_search($dimension, $thumbsList[$resizeMode]) === false) return [];

        return [
                    'url'       => $this->t[$resizeMode][$dimension]['url'],
                    'width'     => $this->t[$resizeMode][$dimension]['width'],
                    'height'    => $this->t[$resizeMode][$dimension]['height'],
                ];
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
                    $this->t[$resizeMode][$targetDimension] 		= $thumb;
                    $this->t[$resizeMode]['i' . $targetDimension] 	= $thumb;
                    $lastResized = $thumb;
                } else {
                    $this->t[$resizeMode][$targetDimension] 		= $lastResized;
                    $this->t[$resizeMode]['i' . $targetDimension] 	= $thumb;
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
}