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
    public $baseUrl = '@web/assets';
    public $css = [
        'css/base.css',
        'css/style.css',
        'css/font-awesome.min.css'
    ];
    public $js = [
        'js/jquery.js',
        'js/common.js',
        'js/jquery.onepage-scroll.js',
        'js/jquery.slick.min.js',
    ];
    public $depends = [
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
