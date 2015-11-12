<?php

namespace app\models;

use app\models\Media;
use Yii;

/**
 * Story class
 */
class MediaUploader {

    const uploadTypeForm            = 'uploadTypeForm';
    const uploadTypeUrl             = 'uploadTypeUrl';
    const uploadTypeLocal           = 'uploadTypeLocal';

    /**
     * Uploads and saves the given file
     * @param mixed $sourceFile
     */
    static function takeFile($target, $fileSource) {
        $this->checkData();
        $uploadInfo = $this->getUploadInfo($fileSource);
        $item = [];

        $mediaOptions = $target->getMediaOptions();

        // Form upload
        if ($uploadInfo['type'] == self::uploadTypeForm) {
            $currentFilePath = $fileSource['tmp_name'];

            if (!empty($fileSource['name'])) $item['filename'] = $this->cleanFileName($this->getFileName($fileSource['name']));
            if (!$this->filename) $this->filename = $item['filename'] = generateFileName();

            $item['ext'] = $this->getFileExt($currentFilePath);
        // Url upload
        } elseif ($uploadInfo['type'] == self::uploadTypeUrl) {
            $currentFilePath = '';

            $item['filename'] = $this->cleanFileName($this->getFileName($currentFilePath));
            $item['ext'] = $this->getFileExt($currentFilePath);
        // Local upload
        } elseif ($uploadInfo['type'] == self::uploadTypeLocal) {
            $currentFilePath = $fileSource;

            $item['filename'] = $this->cleanFileName($this->getFileName($currentFilePath));
            $item['ext'] = $this->getFileExt($currentFilePath);
        } else {
            throw new Exception(Ml::t('Invalid upload type', 'PanelModule.media'));
        }

        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();

        try {
            // When we should keep only one image per target -- do delete
            if (!empty($mediaOptions[Media::keepOnlyOne])) {

            }

            $this->getPathDetails();
            $this->checkFileSize($currentFilePath);
            if ($this->size > $this->getTargetQuota()) throw new Exception(Ml::t('Media storage quota exceeded', 'PanelModule.media'));

            $this->preparePath();

            if (!move_uploaded_file($currentFilePath, $this->_fullPath)) throw new Exception(Ml::t('Failed to move uploaded file', 'PanelModule.media'));

            $this->storeImageResource();

            if (!empty($mediaOptions[Media::saveExif])) $this->getExifData();
            if (!empty($mediaOptions[Media::autoOrient])) $this->autoOrient();
            if ($this->height > $this->width) $this->is_vertical = true;

            $this->save();

            $this->getThumbs();
        } catch(Exception $e) {
            $transaction->rollback();

            throw $e;
        }

        $transaction->commit();
    }

    /**
     * Detects which kind of upload do we handle (form/url/local file)
     * @param mixed $sourceFile
     * @return array
     */
    static function getUploadInfo($filePath) {
        // Check if we handle form uploaded file
        if (is_array($filePath) && !empty($filePath['tmp_name'])) {
            return array('type' => self::uploadTypeForm, 'fileName' => $filePath['tmp_name']);
        // Check if it is just a file URL
        } elseif (preg_match("!^http://!", $filePath)) {
            return array('type' => self::uploadTypeUrl, 'fileName' => $filePath);
        // Check if it is local file
        } elseif ($filePath && file_exists($filePath)) {
            return array('type' => self::uploadTypeLocal, 'fileName' => $filePath);
        } else {
            return array();
        }
    }

    /**
     * Returns the hash based subfolders for the complete path
     * @return string
     * @throws Exception
     */
    static function getSpreadPath() {
        $salt = $this->getSaltValue();
        if (!$salt) throw new Exception(Ml::t('Failed to get salt value for the media item', 'PanelModule.media'));

        $depth = Yii::app()->params['mediaFolderSpreadDepth'];
        if (!$depth) $depth = 2;
        elseif ($depth > 5) $depth = 5;

        $hashString = md5($salt);
        $result = '';

        for ($i = 0; $i < $depth; $i++) {
            $result .= substr($hashString, $i*2, 2);
            if ($i < $depth - 1) $result .= '/';
        }

        return $result;
    }

    /**
     * Returns the cluster name where the item is about to be located
     * @return string
     * @throws Exception
     */
    public function getPartitionName() {
        if (!$this->getSaltValue()) throw new Exception(Ml::t('Failed to get salt value for the media item', 'PanelModule.media'));

        $clusterSize = Yii::app()->params['mediaFileClusterSize'];

        if (!$clusterSize) $n = 0; else $n = ceil($this->getSaltValue() / $clusterSize);

        return 'p' . $n;
    }

    /**
     * Checks all the subfolders of the path and tries to create those that are missing
     * @param string intent what kind of path?
     * @param null $mode
     * @throws Exception
     */
    public function preparePath($intent = 'main', $mode = null) {
        if ($intent == 'main') {
            $storeFolder = $this->_storeFolder ? $this->_storeFolder : $this->getStoreFolder();
        } elseif ($intent == 'thumb') {
            $storeFolder = $this->_thumbFolder ? $this->_thumbFolder : $this->getThumbFolder();
        }

        if (!$storeFolder) throw new Exception(Ml::t('Media full path is not set', 'PanelModule.media'));

        $pathCreate = preg_replace('|^/|i', '', $storeFolder);

        $steps = explode('/', $pathCreate);
        $passed = '';

        foreach ($steps as $step) {
            if (!$step) continue;

            $passed .= '/' . $step;

            if(!is_dir($passed)) {
                if(!mkdir($passed)) throw new Exception(Ml::t('Failed to create media folder', 'PanelModule.media'));

                if($mode) {
                    if(!chmod($passed, $mode)) throw new Exception(Ml::t('Failed to chmod media folder', 'PanelModule.media'));
                }
            }

        }
    }
}