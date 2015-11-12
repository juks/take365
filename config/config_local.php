<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

session_save_path('/home/askarov/sites/new.take365.org/sessions');

return [
    'components' =>
                    [
                        'db' => [
                            'class' => 'yii\db\Connection',
                            'dsn' => 'mysql:host=localhost;dbname=take365',
                            'username' => 'take365',
                            'password' => 'greeting',
                            'charset' => 'utf8',
                        ]
                    ],

    'params' =>
                    [
                        'mediaStorePath'    => '/home/askarov/sites/new.take365.org/www/web/media'
                    ]
];

?>