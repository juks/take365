<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BlogAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/blog.css',
    ];
    public $js = [
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
