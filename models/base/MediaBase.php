<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "media".
 *
 * @property integer $id
 * @property string $target_id
 * @property integer $target_type
 * @property integer $type
 * @property integer $is_deleted
 * @property integer $position
 * @property integer $created_by
 * @property string $filename
 * @property string $ext
 * @property string $title
 * @property integer $size
 * @property integer $width
 * @property integer $height
 * @property integer $is_vertical
 * @property string $partition
 * @property string $path
 * @property string $path_thumb
 * @property integer $format
 * @property integer $time_created
 * @property integer $time_updated
 * @property string $exif
 */
class MediaBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_id', 'target_type', 'type', 'created_by', 'time_created'], 'required'],
            [['target_id', 'target_type', 'type', 'is_deleted', 'position', 'created_by', 'size', 'width', 'height', 'is_vertical', 'format', 'time_created', 'time_updated'], 'integer'],
            [['exif'], 'string'],
            [['filename', 'title'], 'string', 'max' => 100],
            [['ext'], 'string', 'max' => 3],
            [['partition'], 'string', 'max' => 15],
            [['path', 'path_thumb'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'target_id' => 'Target ID',
            'target_type' => 'Target Type',
            'type' => 'Type',
            'is_deleted' => 'Is Deleted',
            'position' => 'Position',
            'created_by' => 'Created By',
            'filename' => 'Filename',
            'ext' => 'Ext',
            'title' => 'Title',
            'size' => 'Size',
            'width' => 'Width',
            'height' => 'Height',
            'is_vertical' => 'Is Vertical',
            'partition' => 'Partition',
            'path' => 'Path',
            'path_thumb' => 'Path Thumb',
            'format' => 'Format',
            'time_created' => 'Time Created',
            'time_updated' => 'Time Updated',
            'exif' =>  'Exif',
        ];
    }

    /**
     * @inheritdoc
     * @return MediaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MediaQueryBase(get_called_class());
    }
}
