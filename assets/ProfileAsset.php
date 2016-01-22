<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ProfileAsset extends AssetBundle
{
    public $basePath = '@webroot/assets';
    public $baseUrl = '@web/assets';
    public $css = [
        'css/profile.css',
    ];
    public $js = [
        'js/jquery.js',
        'js/liveValidation.js',
        'js/plupload/plupload.full.js',
        'js/profile.js',
        'js/profileMedia.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
