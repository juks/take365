<?php
/**
 * Configuration file for the "yii asset" console command.
 */

// In the console environment, some path aliases may not exist. Please define these:
Yii::setAlias('@webroot', __DIR__ . '/../web');
Yii::setAlias('@web', '/');

return [
    // Adjust command/callback for JavaScript files compressing:
    'jsCompressor' => 'java -jar compiler.jar --js {from} --js_output_file {to}',
    // Adjust command/callback for CSS files compressing:
    'cssCompressor' => 'java -jar yuicompressor.jar --type css {from} -o {to}',
    // The list of asset bundles to compress:
    'bundles' => [
        'app\assets\AppAsset',
        'app\assets\FrontAsset',
        'app\assets\ProfileAsset',
        'app\assets\RecoverAsset',
        'app\assets\StoryAsset',
        // 'yii\web\YiiAsset',
        // 'yii\web\JqueryAsset',
    ],
    // Asset bundle for compression output:
    'targets' => [
        'app' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'js.min/app-{hash}.js',
            'css' => 'css.min/app-{hash}.css',
            'depends' => ['app\assets\AppAsset']
        ],
        'front' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'js.min/front-{hash}.js',
            'css' => 'css.min/front-{hash}.css',
            'depends' => ['app\assets\FrontAsset']
        ],
        'profile' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'js.min/profile-{hash}.js',
            'css' => 'css.min/profile-{hash}.css',
            'depends' => ['app\assets\ProfileAsset']
        ],
        'recover' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'js.min/recover-{hash}.js',
            'css' => 'css.min/recover-{hash}.css',
            'depends' => ['app\assets\RecoverAsset']
        ],
        'story' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'js.min/story-{hash}.js',
            'css' => 'css.min/app-{hash}.css',
            'depends' => ['app\assets\StoryAsset']
        ],
    ],
    // Asset manager configuration:
    'assetManager' => [
        'basePath' => '@webroot/assets',
        'baseUrl' => '@web/assets',
    ],
];