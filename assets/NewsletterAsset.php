<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class NewsletterAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'js/panel/newsletter.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
