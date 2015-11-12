<?php

namespace app\models;

use Yii;
use app\models\base\MediaBase;
use app\models\mediaExtra\TMediaFileExtra;
use app\models\mediaExtra\TMediaThumbExtra;
use app\models\mediaExtra\TMediaResizeExtra;
use app\components\Helpers;
use app\components\Ml;
use app\components\traits\TAttachTo;
use app\components\traits\TModelExtra;
use app\components\traits\THasPermission;

/**
 * Story class
 */
class Media extends MediaBase {
    use TAttachTo;
    use THasPermission;
    use TModelExtra;

    use TMediaFileExtra;
    use TMediaThumbExtra;
    use TMediaResizeExtra;

    const uploadTypeForm            = 'uploadTypeForm';
    const uploadTypeUrl             = 'uploadTypeUrl';
    const uploadTypeLocal           = 'uploadTypeLocal';

    const path                      = 'path';
    const alias                     = 'alias';
    const allowedFormats            = 'allowedFormats';
    const maxFileSize               = 'maxFileSize';
    const keepOnlyOne               = 'keepOnlyOne';
    const saveExif                  = 'saveExif';
    const autoOrient                = 'autoOrient';

    const resizeMode                = 'resizeMode';
    const targetDimension           = 'targetDimension';
    const cropOptions               = 'cropOptions';
    const quality                   = 'quality';
    const engine                    = 'engine';
    const engineImageMagick         = 'engineImageMagick';
    const engineGD                  = 'engineGD';

    const resizeFilter              = 'resizeFilter';           // Type of filter used for thumb resize
    const resizeBlur                = 'resizeBlur';             // Filter blur ratio used for thumb resize
    const resizeWidth               = 'width';                  // Image width is converted to this dimension, height goes in proportional way
    const resizeHeight              = 'height';                 // Image height is converted to this dimension, width goes in proportional way
    const resizeMaxSide             = 'maxSide';                // An image size which is longer is converted to this dimension, the other one goes in proportional way
    const resizeMinSide             = 'minSide';                // An image size which is shorter is converted to this dimension, the other one goes in proportional way
    const resizeScaleUp             = 'scaleUp';                // Do image scale up if it is smaller than required
    const resizeFullFill            = 'fullFill';               // A mode when the image should fullfill the given area and be cropped if necessary
    const resizeProportionalFill    = 'proportionalFill';       // A mode when the image sould be shrinked untill it fits the given area without being cropped
    const resizeSquareCrop          = 'squareCrop';             // A mode when the thumb is taken out of the square, located in the middle of given image

    const thumbsList                = 'thumbsList';
    const thumbQuality              = 'thumbQuality';
    
    const forceCreate               = 'forceCreate';
    const autoCreate                = 'autoCreate';
    const isSingle                  = 'isSingle';

    protected $_parent;                 // The parent object we upload media for
    protected $_options;                // Media options

    protected $_fullPath;               // Complete path to the media file
    protected $_storeFolder;            // Path to the folder where media file is to be kept
    protected $_thumbFolder;            // Path to the folder where media thumb file is to be kept
    protected $_thumbFolderReady;       // Is thumb folder ready?
    protected $_saltValue;              // Salt value used to generate md5 subfolders

    protected $_mediaTypeNames;         // Possible media types names
    protected $_mediaOptions;           // Upload options for different media types

    protected $_imageResource;          // Preloaded image resource
    protected $_imageInfo;              // getimagesize() result
    protected $_pathDataReady;          // Whether or not we built the path data already

    /**
     * Sets the parent object for media resourece
     * @param object $parent parent object
     */
    public function setParent(&$parent) {
        $this->checkParent($parent);

        $this->_parent = $parent;
        $this->attachTo($parent);
    }

    /**
     * Checks if parent object is okay
     * @param object $parent parent object
     */
    public function checkParent($parent) {
        if (!method_exists($parent, 'getMediaOptions') || !method_exists($parent, 'getType')) throw new \Exception(Ml::t('Bad Parent Object', 'media'));
    }

    /**
     * Sets the media options
     * @param array $options media options
     */
    public function setOptions($mediaType) {
        $options = $this->_parent->getMediaOptions();

        if (empty($options[$mediaType])) throw new \Exception(Ml::t('No media options', 'media'));

        $this->_options = $options[$mediaType];
    }

     /**
     * Returns single option value for current kind of media
     * @param string $optionName
     * @return null
     * @throws Exception
     */
    public function getOption($optionName) {
        return isset($this->_options[$optionName]) ? $this->_options[$optionName] : null;
    }

    /**
     * Uploads the media resource
     * @param mixed $fileName file resource
     * @param pbject $parent parent object
     */
    public function takeFile($fileSource, $mediaType, $parent = null) {
        if ($parent) $this->setParent($parent);
        $this->setOptions($mediaType);

        if (!$this->_options) throw new \Exception(Ml::t('No media options', 'media'));
        
        $uploadInfo = $this->getUploadInfo($fileSource);
        if (!$uploadInfo) throw new \Exception(Ml::t('Failed to identify upload mode', 'media'));

        $currentFilePath = '';

        if ($uploadInfo['type'] == self::uploadTypeForm && $uploadInfo['type'] == self::uploadTypeLocal) {
            throw new \Exception(Ml::t('Invalid upload type', 'media'));
        }

        $currentFilePath = $uploadInfo['filePath'];
        $this->filename = $uploadInfo['fileName'];
        $this->ext = $this->getFileExt($currentFilePath);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            // When we should keep only one image per target -- do delete
            if (!empty($this->_options[self::isSingle])) $this->cleanPred();

            $this->getPathDetails();
            $this->checkFileSize($currentFilePath);
            $this->checkQuotas();
            $this->preparePath();

            if (!move_uploaded_file($currentFilePath, $this->_fullPath)) throw new \Exception(Ml::t('Failed to move uploaded file', 'media'));

            $this->storeImageResource();

            if (!empty($this->_options[self::saveExif]))    $this->getExifData();
            if (!empty($this->_options[self::autoOrient]))  $this->autoOrient();
            if ($this->height > $this->width)               $this->is_vertical = true;

            $this->save();

            $this->getThumbs();
        } catch(\Exception $e) {
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
    public function getUploadInfo($filePath) {
        // Check if we handle form uploaded file
        if ($filePath instanceof yii\web\UploadedFile) {
            if (empty($filePath->tempName)) throw new \Exception(Ml::t('Can\'t get filename', 'media'));

            return ['type' => self::uploadTypeForm, 'filePath' => $filePath->tempName, 'fileName' => $this->getFileName($filePath->tempName)];
        // Check if it is just a file URL
        } elseif (preg_match("!^http://!", $filePath)) {
            return ['type' => self::uploadTypeUrl, 'filePath' => $filePath, 'fileName' => $this->getFileName($filePath)];
        // Check if it is local file
        } elseif ($filePath && file_exists($filePath)) {
            return ['type' => self::uploadTypeLocal, 'filePath' => $filePath, 'fileName' => $this->getFileName($filePath)];
        } else {
            return null;
        }
    }

    /**
    * Removes previous versions of the same media type
    */
    public function cleanPred() {
        $predCond = $this->getBrotherCondition();
        if ($this->id) $predCond['id'] = ['!=', $this->id];

        $predecessors = $this->find()->where($predCond);

        foreach ($predecessors as $predecessor) {
            $predecessor->markDeleted();
        }
    }

    /**
    * Checks of target quota limit exceeds the file size
    **/
    public function checkQuotas() {
        $storedSize = $this->sqlGetFuncValue('size', $this->getBrotherCondition('target'), 'sum');

        if ($this->size > Helpers::getParam('mediaStorageQuota') - $storedSize) {
            throw new \Exception(Ml::t('Media storage quota exceeded', 'media'));
        }
    }

    /**
     * Stores image resource within the media class instance
     */
    public function storeImageResource($extra = null) {
        if (!$this->_imageResource || !empty($extra['force'])) {
            $this->_imageResource = $this->readImage();
            if ($this->_imageResource) {
                $this->_imageResource->setImageCompressionQuality($this->getOption(self::quality));
                $this->_imageInfo = getimagesize($this->_fullPath, $info);

                $this->format = $this->_imageInfo['2'];
            }
        }
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

    public function getBrotherCondition($type = 'default') {
        if (empty($this->target_id) || empty($this->target_type))
            return null;

        $condition = null;

        if ($type == 'default')
            $condition = ['target_id' => $this->target_id, 'target_type' => $this->target_type, 'type' => $this->type, 'is_deleted' => 0];
        elseif ($type == 'target')
            $condition = ['target_id' => $this->target_id, 'target_type' => $this->target_type, 'is_deleted' => 0];

        return $condition;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getParam($name, $default = null) {
        if (isset(Yii::$app->params[$name])) {
            return Yii::$app->params[$name];
        } else {
            return $default;
        }
    }
}