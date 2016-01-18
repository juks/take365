<?php

namespace app\models\mediaExtra;

use Yii;
use app\components\Ml;

trait TMediaFileExtra {
    /**
     * Wipes away junk characters from filename
     * @param $fileName
     * @return mixed|string
     */
    public function cleanFileName($fileName) {
        $fileName = $this->removeNameJunk($fileName);
        if (!$fileName) $fileName = $this->generateFileName();
    }

    /**
     * Removes junk characters from file name
     *
     * @static
     * @param $fileName
     * @return mixed|string
     */
    public function removeNameJunk($fileName) {
        $fileName = urldecode($fileName);
        $fileName = preg_replace('/[^.a-z0-9_!@$%^&()+={} \[\]\-]/ui', '', $fileName);
        $fileName = preg_replace('/  +/', ' ', $fileName);
        if(preg_match('/^ +$/i', $fileName)) $fileName = ''; else $fileName = str_replace(' ', '_', $fileName);

        return $fileName ? $fileName : 'none';
    }

    /**
     * Get our own file name in case it has got no of it's own
     * @return string
     */
    public function generateFileName() {
        return 'rmf' . $this->getSaltValue() . '_' . rand(1,10000);
    }

    /**
     * Gets the path to where the file should be saved
     * @return mixed
     * @throws \Exception
     */
    public function getPathDetails() {
        if ($this->_pathDataReady) return $this->_fullPath;

        $storePath = $this->getStorePath();

        if (!$storePath) throw new \Exception(Ml::t('Media storage path is not set', 'media'));

        if (!$this->path) {
            $partitionName = $this->getPartitionName();

            $relativePath = $partitionName . '/' . $this->getOption(self::alias) . '/' . $this->getSpreadPath() . '/' . $this->_saltValue;

            $this->partition        = $partitionName;
            $this->path             = $relativePath;
            $this->path_thumb       = 'thumbs/' . $relativePath;
        }

        if (!$this->filename) throw new \Exception(Ml::t('Failed to parse media file name', 'media'));
        if (!$this->ext) throw new \Exception(Ml::t('Failed to parse media file extension', 'media'));

        $this->_fullPath = $this->getFullPath();
        $this->_storeFolder = $this->getStoreFolder();
        $this->_thumbFolder = $this->getThumbFolder();

        $this->_pathDataReady = true;

        return $this->_fullPath;
    }

    /**
     * Checks all the subfolders of the path and tries to create those that are missing
     * @param string intent what kind of path?
     * @param null $mode
     * @throws \Exception
     */
    public function preparePath($intent = 'main', $mode = null) {
        if ($intent == 'main') {
            $storeFolder = $this->_storeFolder ? $this->_storeFolder : $this->getStoreFolder();
        } elseif ($intent == 'thumb') {
            $storeFolder = $this->_thumbFolder ? $this->_thumbFolder : $this->getThumbFolder();
        }

        if (!$storeFolder) throw new \Exception(Ml::t('Media full path is not set', 'media'));

        $pathCreate = preg_replace('|^/|i', '', $storeFolder);

        $steps = explode('/', $pathCreate);
        $passed = '';

        foreach ($steps as $step) {
            if (!$step) continue;

            $passed .= '/' . $step;

            if(!is_dir($passed)) {
                if(!@mkdir($passed)) throw new \Exception('Failed to create media folder ' . $passed);

                if($mode) {
                    if(!chmod($passed, $mode)) throw new \Exception('Failed to chmod media folder ' . $passed);
                }
            }

        }
    }

    /**
     * Checks if media file size exceeds the limit
     * @throws \Exception
     */
    public function checkFileSize($currentPath = null) {
        if (!$this->size) {
            $this->size = $currentPath ? filesize($currentPath) : filesize($this->_fullPath);
        }

        $sizeLimit = $this->getOption(self::maxFileSize);

        if ($sizeLimit && $this->size > $sizeLimit) {
            throw new \Exception(Ml::t('Media file size exceeds allowed limit', 'media'));
        }
    }

    /**
     * Save GD image resource into file
     * @param $image
     * @param $targetFile
     */
    function saveGDImage($targetFile, $image) {
        $imageInfo = $this->getImageInfo();

        # Сохраняем изображение
        if($imageInfo[2] == IMAGETYPE_JPEG)     return imagejpeg($image, $targetFile, $this->getOption(self::quality));
        elseif ($imageInfo[2] == IMAGETYPE_GIF) return imagegif($image, $targetFile);
        elseif ($imageInfo[2] == IMAGETYPE_PNG) return imagepng($image, $targetFile);
    }

    /**
     * Fetches file name from full file path
     *
     * @param $filePath
     * @param bool $cutExt should we take away the file extension?
     * @return string
     */
    public function getFileName($filePath, $cutExt = true) {
        $matches = [];

        if (preg_match('!([^/]+)$!i', $filePath, $matches)) {
            if ($cutExt) {
                $len = strlen($matches[1]);
                $dotPos = strrpos($matches[1], '.');
                if ($dotPos && $len - $dotPos <= 5) $matches[1] = substr($matches[1], 0, $dotPos);
            }

            return strtolower($matches[1]);
        } else {
            return '';
        }
    }

    /**
     * Fetches file extension from file name or detects it according to the file contents
     *
     * @param $filePath
     * @return string
     */
    public function getFileExt($filePath) {
        $len = strlen($filePath);
        $dotPos = strrpos($filePath, '.');
        $fileExt = '';

        if ($dotPos && $len - $dotPos <= 5) {
            $fileExt = strtolower(substr($filePath, $dotPos + 1, $len - $dotPos - 1));
        }

        if ($fileExt == 'jpeg') $fileExt = 'jpg';

        // If we failed to get file extension by it's name - we get it by its contents
        if (!$fileExt || array_search($fileExt, ['jpg', 'jpeg', 'gif', 'png', 'ico']) === false) {
            $imageInfo = getimagesize($filePath);

            if ($imageInfo) {
                if ($imageInfo[2] == IMAGETYPE_JPEG) $fileExt = 'jpg';
                elseif ($imageInfo[2] == IMAGETYPE_GIF) $fileExt = 'gif';
                elseif ($imageInfo[2] == IMAGETYPE_PNG) $fileExt = 'png';
                elseif ($imageInfo[2] == IMAGETYPE_ICO) $fileExt = 'ico';
            }
        }

        return $fileExt;
    }

    /**
     * Returns the cluster name where the item is about to be located
     * @return string
     * @throws \Exception
     */
    public function getPartitionName() {
        if (!$this->getSaltValue()) throw new \Exception(Ml::t('Failed to get salt value for the media item', 'media'));

        $clusterSize = Yii::$app->params['mediaFileClusterSize'];

        if (!$clusterSize) $n = 0; else $n = ceil($this->getSaltValue() / $clusterSize);

        return 'p' . $n;
    }

    /**
     * Returns the hash based subfolders for the complete path
     * @return string
     * @throws \Exception
     */
    public function getSpreadPath() {
        $salt = $this->getSaltValue();
        if (!$salt) throw new \Exception(Ml::t('Failed to get salt value for the media item', 'media'));

        $depth = $this->getParam(self::pMediaFolderSpreadDepth);
        if (!$depth) $depth = 2;
        elseif ($depth > 5) $depth = 5;

        $hashString = md5($salt);
        $result = '';

        for ($i = 0; $i < $depth - 1; $i++) {
            $result .= substr($hashString, $i*2, 2);
            if ($i < $depth - 2) $result .= '/';
        }

        return $result;
    }

  /**
     * Reads image file using GD or ImageMagick
     * @return Imagick|null|resource
     */
    function readImage () {
        $sourceImage = null;

        // IM
        if ($this->getOption(self::engine) == self::engineImageMagick) {
            if (file_exists($this->_fullPath)) {
                $sourceImage = New \Imagick();

                $sourceImage->readImage($this->_fullPath);

                if ($sourceImage) $g = $sourceImage->getImageGeometry();

                $this->width = $g['width'];
                $this->height = $g['height'];
            } else {
                $this->width = 0;
                $this->height = 0;
            }
        // GD
        } else {
            $imageInfo = getimagesize($this->_fullPath);

            $this->width = $imageInfo[0];
            $this->height = $imageInfo[1];

            switch ($imageInfo[2]){
                case IMAGETYPE_JPEG: $sourceImage = imageCreateFromJPEG($this->_fullPath); break;
                case IMAGETYPE_GIF:  $sourceImage = imageCreateFromGIF($this->_fullPath);  break;
                case IMAGETYPE_PNG:  $sourceImage = imageCreateFromPNG($this->_fullPath);  break;
            }
        }

        return $sourceImage;
    }

     /**
     * Returns the full path to the media item
     * @return string
     */
    public function getFullPath() {
        return $this->getStoreFolder() . '/' . $this->filename . '.' . $this->ext;
    }

    /**
     * Returns the media item common storage folder location
     * @return mixed
     */
    public function getStorePath() {
        return Yii::$app->params['mediaStorePath'];
    }

    /**
     * Returns the media item storage path relative to the common storage path
     * @return string
     */
    public function getStoreFolder() {
        return $this->getStorePath() . '/' . $this->path;
    }

    /**
     * Return media thumb storage path relative to the common storage path
     * @return string
     */
    public function getThumbFolder($absolute = true) {
        $path = '/thumbs/' . $this->path;

        return $absolute ? $this->getStorePath() . $path : $path;
    }

    /**
     * Returns the full path to thumb file depending on its dimensions
     * @param array $dimensions
     */
    public function getThumbPath($dimensions, $absolute = true) {
        return $this->getThumbFolder($absolute) . '/' . $this->filename . '_' . $dimensions['width'] . 'x' . $dimensions['height'] . '.' . $this->ext;
    }

    /**
     * recursively deletes given path
     * @static
     * @param $path
     * @return bool|null
     * @throws \Exception
     */
    public function deleteRecursive($path) {
        if (is_dir($path) && !is_link($path)) {
            $dh = opendir($path);
            if ($dh) {
                while (($sf = readdir($dh)) !== false) {
                    if ($sf == '.' || $sf == '..') continue;

                    if(!self::deleteRecursive($path . '/' . $sf)) throw new \Exception('Unable to Delete Folder');
                }

                closedir($dh);
            }

            return rmdir($path);
        } elseif(file_exists($path)) {
            return unlink($path);
        } else {
            return null;
        }
    }
}