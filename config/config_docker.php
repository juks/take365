<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

define('PATH_ROOT', '/var/www/');

session_save_path('/opt/sites/take365.org/sessions');

return [
    'components' =>
                    [
                        'db' => [
                            'class' => 'yii\db\Connection',
                            'dsn' => 'mysql:host=db;dbname=take365',
                            'username' => 'take365',
                            'password' => 'greeting',
                            'charset' => 'utf8',
                            'enableSchemaCache' => true,
                            'schemaCacheDuration' => 3600
                        ],

                        'db1' => [
                            'class' => 'yii\db\Connection',
                            'dsn' => 'mysql:host=db;dbname=take365',
                            'username' => 'take365',
                            'password' => 'greeting',
                            'charset' => 'utf8',
                        ],

                        'cache' => [
                            'class' => 'yii\caching\MemCache',
                            'keyPrefix' => 'take365',
                            'useMemcached' => true,
                            'servers' => [
                                    [
                                        'host' => 'localhost',
                                        'port' => 11211,
                                        'weight' => 100,
                                    ],
                            ],
                        ],

                        'authClientCollection' => [
                            'class' => 'yii\authclient\Collection',
                            'clients' => [
                                'facebook' => [
                                    'clientId' => 301096096567708,
                                    'clientSecret' => 'c4bbbe8d885fc122a3928c3d51f3a6a4',
                                ],
                                /*'twitter' => [
                                    'consumerKey' => 'TZwKa0WtkYlNjQW6CN88908hS',
                                    'consumerSecret' => 'LrAgBNAdoIt2RfaY1rO6TmyVRLRdLjwykC9QzI3kB6SHN2ly9m',
                                ],*/
                               'vkontakte' => [
                                    'clientId' => 5301500,
                                    'clientSecret' => 'nGjsB2soISz1nKZ9nZfQ'
                                ],
                            ],
                        ],
                    ],

    'params'    =>
                    [
                        'mediaStorePath'    => PATH_ROOT . 'web/media',
                        'mediaImportPath'   => '/var/www/public/media',
                        'mediaHost'         => 'http://take365.vasa.tech',
                        'mediaBaseUrl'      => '/media',
                        'mosaicPath'        => PATH_ROOT . 'web/media/mosaics',
                        'tmpPath'           => PATH_ROOT . 'tmp',
                        'fontsPath'         => PATH_ROOT . 'misc/fonts',
                        'storage'           => ['storePath' => PATH_ROOT . 'web/storage']
                    ]
];

?>