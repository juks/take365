<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'api' => ['class' => 'app\modules\api\ApiModule'] 
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '0QaN9P5ZvXOdB8OdPowGRk75PIf-0Wu4',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            //'class' => 'app\models\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace', 'info'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require(dirname(__FILE__) . '/' . '_routes.php')
        ],
        'view' => [
            'class' => 'app\components\MyView',
            'renderers' => [
                'tpl' => [
                    'class' => 'app\components\BlitzTemplate'
                    //'cachePath' => '@runtime/Smarty/cache',
                ]]
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['user'], 
        ],
        'response' => [
                        //'format' => api\components\web\Response::FORMAT_JSON,
                        //'charset' => 'UTF-8',
                        'formatters' => [
                                'json' => 'app\components\PrettyJsonResponseFormatter',
                        ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

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
