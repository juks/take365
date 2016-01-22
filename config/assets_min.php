<?php
/**
 * This file is generated by the "yii asset" command.
 * DO NOT MODIFY THIS FILE DIRECTLY.
 * @version 2016-01-22 21:59:13
 */
return [
    'app' => [
        'class' => 'yii\\web\\AssetBundle',
        'basePath' => '@webroot/min',
        'baseUrl' => '@web/min',
        'js' => [
            'js/app-186ab0c22a9a8b65baef08f2409fef06.js',
        ],
        'css' => [
            'css/app-a6b95740d6e2aecf842a511175a07ac1.css',
        ],
    ],
    'front' => [
        'class' => 'yii\\web\\AssetBundle',
        'basePath' => '@webroot/min',
        'baseUrl' => '@web/min',
        'js' => [
            'js/front-231f487e116d2114e649d65918bd3ffe.js',
        ],
        'css' => [
            'css/front-17e2d85462f630aa21714e2b4b8c5c6c.css',
        ],
    ],
    'profile' => [
        'class' => 'yii\\web\\AssetBundle',
        'basePath' => '@webroot/min',
        'baseUrl' => '@web/min',
        'js' => [
            'js/profile-04ccb5a650e854001bf356c7ad558700.js',
        ],
        'css' => [
            'css/profile-de8a9e66dbe275a60aa054399c39a90d.css',
        ],
    ],
    'recover' => [
        'class' => 'yii\\web\\AssetBundle',
        'basePath' => '@webroot/min',
        'baseUrl' => '@web/min',
        'js' => [
            'js/recover-bf0de58c5461bb1f2737855c145ba68d.js',
        ],
        'css' => [],
    ],
    'story' => [
        'class' => 'yii\\web\\AssetBundle',
        'basePath' => '@webroot/min',
        'baseUrl' => '@web/min',
        'js' => [
            'js/story-d87587d38a022d64e2389fa38bbb0062.js',
        ],
        'css' => [
            'css/app-fb05d233d3e95c3146974523fb31fee1.css',
        ],
    ],
    'app\\assets\\AppAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app',
        ],
    ],
    'app\\assets\\FrontAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'front',
        ],
    ],
    'app\\assets\\ProfileAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'profile',
        ],
    ],
    'app\\assets\\RecoverAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\assets\\AppAsset',
            'recover',
        ],
    ],
    'app\\assets\\StoryAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\assets\\AppAsset',
            'story',
        ],
    ],
];