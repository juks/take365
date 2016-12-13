<?php

namespace app\models\mediaExtra;

use Yii;
use app\models\base\MediaBase;
use app\components\Download;
use app\components\Helpers;
use app\components\HelpersTxt;
use app\components\Ml;
use app\components\traits\TAttachTo;
use app\components\traits\TModelExtra;
use app\components\traits\THasPermission;
use app\components\traits\TAuthor;

/**
 * Story class
 */
class MediaCore extends MediaBase {
    use TAttachTo;
    use THasPermission;
    use TModelExtra;

    use \app\models\mediaExtra\TMediaFileExtra;
    use \app\models\mediaExtra\TMediaThumbExtra;
    use \app\models\mediaExtra\TMediaImageExtra;
    use \app\models\mediaExtra\TMediaUrlExtra;
    use \app\models\mediaExtra\TMediaDeleteExtra;
    use TAuthor;

    const uploadTypeForm            = 'uploadTypeForm';
    const uploadTypeUrl             = 'uploadTypeUrl';
    const uploadTypeLocal           = 'uploadTypeLocal';

    const mediaTypeId               = 'typeId';
    const targetType                = 'targetType';
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
    const resizeScaleUpDimension    = 'scaleUpDimension';       // Do image scale up if it is smaller than required but not greater than this dimension
    const resizeFullFill            = 'fullFill';               // A mode when the image should fullfill the given area and be cropped if necessary
    const resizeProportionalFill    = 'proportionalFill';       // A mode when the image sould be shrinked untill it fits the given area without being cropped
    const resizeSquareCrop          = 'squareCrop';             // A mode when the thumb is taken out of the square, located in the middle of given image

    const thumbsList                = 'thumbsList';
    const thumbQuality              = 'thumbQuality';
    const mainThumbDimension        = 'mainThumbDimension';
    const largeThumbDimension       = 'largeThumbDimension';
    
    const thumbsCreate              = 'thumbsCreate';
    const skipThumbsCreate          = 'skipThumbsCreate';
    const forceThumbsCreate         = 'forceThumbsCreate';
    const cleanPrev                 = 'cleanPrev';

    public $i = [];                                             // Image url and dimensions
    public $t = [];                                             // Thumbs urls and dimensions

    protected $_parent;                                         // The parent object we upload media for
    protected $_options;                                        // Media options

    protected $_fullPath;                                       // Complete path to the media file
    protected $_storeFolder;                                    // Path to the folder where media file is to be kept
    protected $_thumbFolder;                                    // Path to the folder where media thumb file is to be kept
    protected $_thumbFolderReady;                               // Is thumb folder ready?
    protected $_saltValue;                                      // Salt value used to generate md5 subfolders

    protected $_mediaTypeNames;                                 // Possible media types names
    protected $_mediaOptions;                                   // Upload options for different media types

    protected $_imageResource;                                  // Preloaded image resource
    protected $_imageInfo;                                      // getimagesize() result
    protected $_pathDataReady;                                  // Whether or not we built the path data already
    
    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            $this->time_created = time();
            if (!$this->created_by) {
                $user = Yii::$app->user;
                $this->created_by = $user->id;
            }
        } else {
            $this->time_updated = time();
        }

        if (!$this->_oldAttributes['description'] !== $this->description) $this->description_jvx = HelpersTxt::simpleText($this->description);

        return parent::beforeValidate();
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        return [
            'url'      => 'url',
            'width'    => 'width',
            'height'   => 'height'
        ];
    }

    public function afterFind() {
        $this->getPathDetails();
        $this->getThumbs();
    }

    /**
    * Returns the field name that represents the owner user id
    */
    public function getCreatorIdField() {
        return 'created_by';
    }

    /**
    * Returns the criteria whether or not this item is public
    */
    public function getIsPublic() {
        return !$this->is_hidden;
    }

    /**
     * Sets the parent object for media resourece
     * @param object $parent parent object
     */
    public function setParent(&$parent) {
        $this->_parent = $parent;
        $this->attachTo($parent);
    }

    public static function getMediaOptions($type = null) {
        if (!$type) {
            return static::$_globalOptions;
        } else {
            $typeId = self::getTypeByAlias($type);

            if (!empty(static::$_globalOptions[$typeId])) {
                return static::$_globalOptions[$typeId];
            } else {
                return null;
            }
        }
    }

     /**
     * Returns single option value for current kind of media
     * @param string $optionName
     * @return boolean
     * @throws Exception
     */
    public function getOption($optionName) {
        return isset(static::$_globalOptions[$this->type][$optionName]) ? static::$_globalOptions[$this->type][$optionName] : null;
    }

     /**
     * Finds media type by alias
     * @param string $alais
     * @return integet
     * @throws Exception
     */
    public static function getTypeByAlias($alias) {
        foreach (static::$_globalOptions as $type => $data) {
            if ($data[MediaCore::alias] == $alias) return $type; 
        }

        return null;
    }

    /**
     * Uploads the media resource
     * @param mixed $fileName file resource
     * @param object $parent parent object
     * @param array $extra extra data
     */
    public function takeFile($fileSource, $mediaType, $parent = null, $extra = []) {
        if ($parent) $this->setParent($parent);

        if (is_int($mediaType)) $this->type = $mediaType; else $this->type = self::getTypeByAlias($mediaType);

        if (!$this->type)                                           throw new \Exception(Ml::t('No media type Id', 'media'));
        if ($this->type != $parent->getType())                      throw new \Exception(Ml::t('Wrong target type id', 'media'));
        if ($this->type != $this->getOption(MediaCore::targetType)) throw new \Exception(Ml::t('Media target type id is not allowed for this object', 'media'));
        
        $uploadInfo = $this->getUploadInfo($fileSource);

        if (!$uploadInfo)                                           throw new \Exception(Ml::t('Failed to identify upload mode', 'media'));

        if (array_search($uploadInfo['type'], [self::uploadTypeForm, self::uploadTypeLocal, self::uploadTypeUrl]) === null) throw new \Exception(Ml::t('Invalid upload type', 'media'));

        $currentFilePath    = $uploadInfo['filePath'];
        $this->filename     = $uploadInfo['fileName'];
        $this->ext = $this->getFileExt($currentFilePath);

        if (!empty($extra['fields'])) $this->load($extra['fields']);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            // When we should keep only one image per target -- do delete
            if ($this->getOption(self::cleanPrev)) $this->cleanPred();

            $this->getPathDetails();
            $this->checkQuotas();
            $this->preparePath();

            // Form upload
            if ($uploadInfo['type'] == self::uploadTypeForm) {
                $this->checkFileSize($currentFilePath);
                if (!move_uploaded_file($currentFilePath, $this->_fullPath)) throw new \Exception('Failed to move uploaded file: ' . $currentFilePath . ' to ' . $this->_fullPath);
            // Local file
            } elseif ($uploadInfo['type'] == self::uploadTypeLocal) {
                $this->checkFileSize($currentFilePath);
                if (!copy($currentFilePath, $this->_fullPath)) throw new \Exception('Failed to copy uploaded file: ' . $currentFilePath . ' to ' . $this->_fullPath);
            // URL upload
            } elseif ($uploadInfo['type'] == self::uploadTypeUrl) {
                $tmpPath = self::getParam('tmpPath');
                if (!$tmpPath) throw new Exception('Temporary file path is not configured');
                
                $download = new Download($tmpPath);
                $downloadedFile = $download->get($currentFilePath, $this->getOption(self::maxFileSize));

                if (!rename($downloadedFile['filePath'], $this->_fullPath)) throw new \Exception('Failed to move downloaded file: ' . $currentFilePath . ' to ' . $this->_fullPath);
            }

            $this->storeImageResource();

            if ($this->getOption(self::saveExif))    $this->getExifData();
            if ($this->getOption(self::autoOrient))  $this->autoOrient();
            if ($this->height > $this->width)        $this->is_vertical = true;

            if (!$this->save()) {
                throw new \Exception($this->modelErrorsToString());
                
            }

            $this->getThumbs($extra);
        } catch(\Exception $e) {
            $transaction->rollback();

            throw $e;
        }

        $transaction->commit();

        if (method_exists($this, 'afterUpload')) $this->afterUpload();
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

    /**
    * Removes previous versions of the same media type
    */
    public function cleanPred() {
        $predCond = $this->getBrotherCondition();
        if ($this->id) $predCond['id'] = ['!=', $this->id];

        $predecessors = $this->find()->where($predCond)->all();

        $lastTitle = '';
        $lastDescription = '';
        $lastDescriptionJvx = '';

        foreach ($predecessors as $predecessor) {
            if ($predecessor->title) $lastTitle = $predecessor->title;
            if ($predecessor->description) {
                $lastDescription = $predecessor->description;
                $lastDescriptionJvx = $predecessor->description_jvx;
            }

            $predecessor->markDeleted();
        }

        if ($lastTitle || $lastDescription) {
            $this->title = $lastTitle;
            $this->description = $lastDescription;
            $this->description_jvx = $lastDescriptionJvx;
            $this->save();
        }
    }

    /**
    * Checks of target quota limit exceeds the file size
    **/
    public function checkQuotas() {
        if (!Helpers::getParam('media/storageQuota')) {
            throw new \Exception(Ml::t('Media storage quota not set', 'media'));    
        }

        $storedSize = self::sqlGetFuncValue('size', $this->getBrotherCondition('target'), 'sum');

        if ($this->size > Helpers::getParam('media/storageQuota') - $storedSize) {
            throw new \Exception(Ml::t('Media storage quota exceeded', 'media'));
        }
    }

    /**
     * Returns stored image resource if there is one
     */
    public function getImageResource() {
        return !empty($this->_imageResource) ? $this->_imageResource : null;
    }

    /**
     * Returns image info array
     * @return null
     */
    public function getImageInfo() {
        return !empty($this->_imageInfo) ? $this->_imageInfo : null;
    }

    /**
     * Stores image resource within the media class instance
     */
    public function storeImageResource($extra = null) {
        if (!$this->_imageResource || !empty($extra['force'])) {
            $this->_imageResource = $this->readImage();

            if ($this->_imageResource) {
                if ($this->getOption(self::engine) == self::engineImageMagick) {
                    $this->_imageResource->setImageCompressionQuality($this->getOption(self::quality));
                } 

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
    public static function getParam($name, $default = null) {
        return Helpers::getParam($name, $default);
    }
}