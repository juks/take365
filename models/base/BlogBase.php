<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "blog".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $time_created
 * @property integer $time_updated
 */
class BlogBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'time_created', 'time_updated'], 'integer']
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
        ];
    }

    /**
     * @inheritdoc
     * @return BlogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BlogQueryBase(get_called_class());
    }
}
