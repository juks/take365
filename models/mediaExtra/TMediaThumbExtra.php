<?php

namespace app\models\mediaExtra;

use Yii;
use add\models\media;

trait TMediaThumbExtra {
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

        if(empty($this->_mediaOptions[$this->type][self::thumbsList]) || !is_array($this->_mediaOptions[$this->type][self::thumbsList]) || !count($this->_mediaOptions[$this->type][self::thumbsList])) return;

        foreach ($this->_mediaOptions[$this->type][self::thumbsList] as $resizeMode => $resizeDimensions) {
            foreach ($resizeDimensions as $targetDimension) {
                $thumb = $this->makeThumb($resizeMode, $targetDimension, $extra);;
                
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

        if (!$this->_thumbFolderReady) {
            $this->preparePath('thumb');
            $this->_thumbFolderReady = true;
        }

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

            if (!empty($extra[self::forceCreate]) || (!empty(Yii::app()->params['mediaThumbsAutoCreate']) || !empty($extra[self::autoCreate])) && !file_exists($thumbPath)) {
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