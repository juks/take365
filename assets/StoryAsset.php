<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class StoryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/story.css',
    ];
    public $js = [
        'js/story.js'
    ];
    public $depends = [];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}