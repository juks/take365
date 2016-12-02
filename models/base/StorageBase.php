<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "storage".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $time_created
 * @property integer $time_updated
 * @property integer $time_delete
 * @property string $filename
 * @property string $ext
 * @property string $mime
 * @property integer $size
 * @property string $partition
 * @property string $path
 * @property integer $is_deleted
 * @property string $key
 */
class StorageBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'storage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename'], 'required'],
            [['created_by', 'time_created', 'time_updated', 'time_delete', 'size', 'is_deleted'], 'integer'],
            [['filename'], 'string', 'max' => 128],
            [['ext'], 'string', 'max' => 5],
            [['mime'], 'string', 'max' => 50],
            [['partition'], 'string', 'max' => 25],
            [['path', 'key'], 'string', 'max' => 255],
            [['key'], 'checkKeyExists']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'Created By',
            'time_created' => 'Time Created',
            'time_updated' => 'Time Updated',
            'time_delete' => 'Time Delete',
            'filename' => 'Filename',
            'ext' => 'Ext',
            'mime' => 'Mime',
            'size' => 'Size',
            'partition' => 'Partition',
            'path' => 'Path',
            'is_deleted' => 'Is Deleted',
            'key' => 'Key',
        ];
    }

    /**
     * @inheritdoc
     * @return StorageQueryBase the active query used by this AR class.
     */
    public static function find()
    {
        return new StorageQueryBase(get_called_class());
    }

    public function checkKeyExists($attribute) {
        if (!$this->$attribute) return;

        $cond = ['key' => $this->$attribute, 'is_deleted' => 0];
        if ($this->id) $cond['id'] = ['!=', $this->id];

        if (self::find()->where(static::makeCondition($cond))->count()) $this->addError($attribute, Ml::t('This file key is already used'));
    }
}
