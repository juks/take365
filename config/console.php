<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
	          'defaultRoles' => ['admin', 'user']
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        'main' => 'main.php',
                    ],
                ],
            ],
        ],
        'urlManager' => [
            'baseUrl' => '/',
            'hostInfo' => 'http://new.take365.org'
        ]
    ],
    'params' => $params,
];

/**
* Next we check for the local server configurations to come
*/
$localConfig = dirname(__FILE__) . '/' . 'config_local.php';
if(file_exists($localConfig)) $config = array_merge_recursive(require($localConfig), $config);

$extraFileNames = [];
if (!empty($_SERVER['APP_STAGE']) && $_SERVER['APP_STAGE'] == 'devel' || defined('APP_STAGE') && APP_STAGE == 'devel') {
   $extraFileNames[] = dirname(__FILE__) . '/' . 'config_devel.php';
} else {
   $extraFileNames[] = dirname(__FILE__) . '/' . 'config_production.php';
}

if ($extraFileNames) {
   foreach ($extraFileNames as $fileName) {
       if (file_exists($fileName)) {
           $config = array_merge_recursive(require($fileName), $config);
       }
   }
}

return $config;