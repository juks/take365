<?php

namespace app\models;

use app\models\base\StorageBase;
use Yii;
use app\components\Helpers;
use app\components\Download;
use app\components\Ml;
use app\components\traits\TModelExtra;

/**
 * Feed class
 */
class Storage extends StorageBase {
	use TModelExtra;

    const uploadTypeForm            = 'uploadTypeForm';
    const uploadTypeUrl             = 'uploadTypeUrl';
    const uploadTypeLocal           = 'uploadTypeLocal';

    protected $_fullPath;           // Complete path to the media file
    protected $_storeFolder;        // Path to the folder where media file is to be kept
    protected $_saltValue;          // Salt value used to generate md5 subfolders
    protected $_pathDataReady;      // Whether or not we built the path data already

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            $this->time_created = time();
            if (!$this->created_by) {
                $user = Yii::$app->user;

                $this->created_by = $user->isGuest ? 0 : $user->id;
            }
        } else {
            $this->time_updated = time();
        }

        return parent::beforeValidate();
    }

    /**
     * Uploads file resource
     * @param $fileSource
     * @throws Exception
     * @throws ModelException
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function takeFile($fileSource) {
        $uploadInfo = $this->getUploadInfo($fileSource);

        if (!$uploadInfo)                                           throw new \Exception(Ml::t('Failed to identify upload mode', 'storage'));

        if (array_search($uploadInfo['type'], [self::uploadTypeForm, self::uploadTypeLocal, self::uploadTypeUrl]) === null) throw new \Exception(Ml::t('Invalid upload type', 'storage'));

        $currentFilePath    = $uploadInfo['filePath'];
        $this->filename     = $uploadInfo['fileName'];
        $this->ext = $this->getFileExt($currentFilePath);

        if (!empty($extra['fields'])) $this->load($extra['fields']);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $this->getPathDetails();
            $this->preparePath();

            // Form upload
            if ($uploadInfo['type'] == self::uploadTypeForm) {
                $this->checkFileSize($currentFilePath);
                if (!move_uploaded_file($currentFilePath, $this->_fullPath)) throw new \Exception('Failed to move uploaded file: ' . $currentFilePath . ' to ' . $this->_fullPath);
                $mime = mime_content_type($this->_fullPath);
                if ($mime) $this->mime = $mime;
            // Local file
            } elseif ($uploadInfo['type'] == self::uploadTypeLocal) {
                $this->checkFileSize($currentFilePath);
                if (!copy($currentFilePath, $this->_fullPath)) throw new \Exception('Failed to copy uploaded file: ' . $currentFilePath . ' to ' . $this->_fullPath);
                $mime = mime_content_type($this->_fullPath);
                if ($mime) $this->mime = $mime;
            // URL upload
            } elseif ($uploadInfo['type'] == self::uploadTypeUrl) {
                $tmpPath = Helpers::getParam('tmpPath');
                if (!$tmpPath) throw new Exception('Temporary file path is not configured');

                $download = new Download($tmpPath);
                $downloadedFile = $download->get($currentFilePath, Helpers::getParam('storage/maxFileSize'));
                $this->size = $downloadedFile['size'];

                $mime = mime_content_type($downloadedFile['filePath']);
                if ($mime) $this->mime = $mime;

                if (!rename($downloadedFile['filePath'], $this->_fullPath)) throw new \Exception('Failed to move downloaded file: ' . $currentFilePath . ' to ' . $this->_fullPath);
            }

            if (!$this->save()) {
                throw new \Exception($this->modelErrorsToString());
            }

        } catch(\Exception $e) {
            $transaction->rollback();

            throw $e;
        }

        $transaction->commit();

        if (method_exists($this, 'afterUpload')) $this->afterUpload();
    }

    /**
     * Retrieve item by key
     * @param $key
     * @return StorageBase|array|null
     */
    public static function getByKey($key) {
        return self::find()->where(['key' => $key, 'is_deleted' => 0])->one();
    }

    /**
     * Detects which kind of upload do we handle (form/url/local file)
     * @param mixed $sourceFile
     * @return array
     */
    public function getUploadInfo($filePath) {
        // Check if we handle form uploaded file
        if ($filePath instanceof yii\web\UploadedFile) {
            if (empty($filePath->tempName)) throw new \Exception(Ml::t('Can\'t get filename', 'media'));

            return ['type' => self::uploadTypeForm, 'filePath' => $filePath->tempName, 'fileName' => $this->getFileName($filePath->name)];
            // Check if it is just a file URL
        } elseif (preg_match("!^https?://!", $filePath)) {
            return ['type' => self::uploadTypeUrl, 'filePath' => $filePath, 'fileName' => $this->getFileName($filePath)];
            // Check if it is local file
        } elseif ($filePath && file_exists($filePath)) {
            return ['type' => self::uploadTypeLocal, 'filePath' => $filePath, 'fileName' => $this->getFileName($filePath)];
        } else {
            return null;
        }
    }

    public function cleanFileName($filename) {
        $filename = $this->removeNameJunk($filename);
        if (!$filename) $filename = $this->generateFileName();

        return $filename;
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
        $fileName = preg_replace('/[^.a-z0-9_!@$^&()+={} \[\]\-]/ui', '', $fileName);
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

            $relativePath = $partitionName . '/' . $this->getSpreadPath() . '/' . $this->_saltValue;

            $this->partition        = $partitionName;
            $this->path             = $relativePath;
        }

        if (!$this->filename) throw new ModelException(Ml::t('Failed to parse media file name', 'media'));
        if (!$this->ext) throw new ModelException(Ml::t('Unsupported file type', 'media'));

        $this->_fullPath = $this->getFullPath();
        $this->_storeFolder = $this->getStoreFolder();

        $this->_pathDataReady = true;

        return $this->_fullPath;
    }

    /**
     * Checks all the subfolders of the path and tries to create those that are missing
     * @param string intent what kind of path?
     * @param null $mode
     * @throws \Exception
     */
    public function preparePath($mode = null) {
        $storeFolder = $this->_storeFolder ? $this->_storeFolder : $this->getStoreFolder();

        if (!$storeFolder) throw new \Exception(Ml::t('Media full path is not set', 'media'));

        $pathCreate = preg_replace('|^/|i', '', $storeFolder);

        $steps = explode('/', $pathCreate);
        $passed = '';

        foreach ($steps as $step) {
            if (!$step) continue;

            $passed .= '/' . $step;

            if(!is_dir($passed)) {
                if(!@mkdir($passed)) throw new \Exception('Failed to create storage folder ' . $passed);

                if($mode) {
                    if(!chmod($passed, $mode)) throw new \Exception('Failed to chmod storage folder ' . $passed);
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

        $sizeLimit = Helpers::getParam('storage/maxFileSize');

        if ($sizeLimit && $this->size > $sizeLimit) {
            throw new \app\components\ModelException(Ml::t('Storage file size exceeds allowed limit', 'storage'));
        }
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

        return $fileExt;
    }

    /**
     * Returns the cluster name where the item is about to be located
     * @return string
     * @throws \Exception
     */
    public function getPartitionName() {
        if (!$this->getSaltValue()) throw new \Exception(Ml::t('Failed to get salt value for the media item', 'media'));

        $clusterSize = Helpers::getParam('media/fileClusterSize');

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

        $depth = helpers::getParam('storage/folderSpreadDepth');
        if (!$depth) $depth = 1;
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
     * Gets some entity-related unique value (entity id or table auto_increment)
     */
    public function getSaltValue() {
        if ($this->_saltValue) return $this->_saltValue;

        if ($this->id) {
            if (!$this->_saltValue) $this->_saltValue = $this->id;

            return $this->id;
        }

        $command = Yii::$app->db->createCommand("show table status where name = '" . $this->tableName() . "'");
        $reader = $command->query();
        $row = $reader->read();

        if (!empty($row['Auto_increment'])) {
            $this->_saltValue = $row['Auto_increment'];

            return $this->_saltValue;
        } else {
            return null;
        }
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
        return Helpers::getParam('storage/storePath');
    }

    /**
     * Returns the item storage path relative to the common storage path
     * @return string
     */
    public function getStoreFolder() {
        return $this->getStorePath() . '/' . $this->path;
    }

    /**
     * recursively deletes given path
     * @static
     * @param $path
     * @return bool|null
     * @throws \Exception
     */
    public static function deleteRecursive($path) {
        if (is_dir($path) && !is_link($path)) {
            $dh = opendir($path);
            if ($dh) {
                while (($sf = readdir($dh)) !== false) {
                    if ($sf == '.' || $sf == '..') continue;

                    if(!self::deleteRecursive($path . '/' . $sf)) throw new \Exception('Unable to Delete Folder ' . $path . '/' . $sf);
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

    /**
     * Marks item for deletion
     * @param $replace was this file replaced by other
     */
    public function markDeleted($replace = false) {
        $this->is_deleted = 1;
        $this->time_deleted = time();

        Helpers::transact(function() use ($replace) {
            if ($this->save()) {
                if (method_exists($this, 'afterMediaDelete')) $this->afterMediaDelete($replace);
            }
        });
    }

    /**
     * Recovers item from deleetd state
     */
    public function recoverDeleted() {
        $this->is_deleted = 0;
        $this->time_deleted = 0;
        $this->save();
    }
}