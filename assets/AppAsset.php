<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'blocks/Search/search.css',
        'css/react.css',
        'css/reset.css',
        'css/style.css',
        'css/blog.css',
        'css/font-awesome.min.css'
    ];
    public $js = [
        'js/common.js',
        'js/smartdate.js',
        'js/react.js'
    ];
    public $depends = [
      //'yii\web\JqueryAsset'
        'app\assets\JqueryAsset'
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
