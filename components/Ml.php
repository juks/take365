<?php

namespace app\components;

use Yii;
use app\components\Language;

class Ml {
    public static $preferredLanguage;

    /**
     * Yii::t addon. Makes it easier to reference ML tables from different modules
     * Eg.: Ml::t('about', 'moduleName.tableName')
     *      Ml::t('about', 'moduleName')
     * @static
     * @param $string
     * @param null $category
     * @param null $params
     * @return mixed
     */
    static function t($string, $category = null, $params = null) {
        $names = self::getCategoryModuleName($category);

        if (!self::$preferredLanguage) {
            self::$preferredLanguage = Language::detect();
        }

        return Yii::t($names['category'], $string, $params, null, self::$preferredLanguage);
    }

    static function getCategoryModuleName($category) {
        $result = ['category' => '', 'module' => ''];

        if (($i = strpos($category, '.')) !== false) {
            $result['module'] = substr($category, 0, $i);
            $result['category'] = substr($category, $i + 1, strlen($category) - 1);
        } elseif (preg_match('/Module$/', $category)) {
            $result['category'] .= $category . '.core';
        }

        if (!$result['category']) $result['category'] = 'core';
        if ($result['module']) $result['category'] = $result['module']  . '.' . $result['category'];

        return $result;
    }
}