<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class StoryAsset extends AssetBundle
{
    public $basePath = '@webroot/assets';
    public $baseUrl = '@web/assets';
    public $css = [
        'css/photoview.css',
        'css/story.css',
    ];
    public $js = [
        'js/plupload/plupload.full.js',
        'js/photoview.js',
        'js/story.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
