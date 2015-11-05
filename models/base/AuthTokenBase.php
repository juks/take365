<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "auth_token".
 *
 * @property integer $user_id
 * @property integer $time_created
 * @property integer $time_used
 * @property integer $ip
 * @property string $key
 */
class AuthTokenBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'time_created', 'time_expire'], 'required'],
            [['user_id', 'time_created', 'time_used', 'time_expire', 'ip_created'], 'integer'],
            [['key'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'time_created' => 'Time Created',
            'time_used' => 'Time Used',
            'ip_created' => 'Ip',
            'time_expire' => 'Expiration time',
            'key' => 'Key',
        ];
    }

    /**
     * @inheritdoc
     * @return AuthTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuthTokenQueryBase(get_called_class());
    }
}
