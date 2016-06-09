<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JqueryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    /*public $css = [
    ];*/
    public $js = [
        //'js/jquery.js',
        'js/jquery.onepage-scroll.js',
        'js/jquery.slick.min.js'
    ];
    public $depends = [
        //'yii\web\JqueryAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
