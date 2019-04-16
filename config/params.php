<?php

return [
    'adminEmail' 					=> 'admin@example.com',
    'projectName'					=> 'take365',
    'projectUrl'                    => 'https://take365.org/',
    'projectRobotEmail'             => 'noreply@take365.org',
    'newsletterEmail'               => 'newsletter@take365.org',

    'user'       => [
                        'recoveryLifetime'      => 7200
                    ],

    'story'      => [
                        'deletedLifetime'       => 86400 * 2
                    ],

    'media'      => [
                        'storageQuota'          => 10737418240, // 10G
                        'folderSpreadDepth'     => 1,
                        'fileClusterSize'       => 10000,
                        'mediaThumbsAutoCreate' => false,
                        'deletedLifetime'       => 86400 * 30
                    ],

    'storage'    => [
                        'folderSpreadDepth'     => 1,
                        'maxFileSize'           => 1048576
                    ],

    'mQueue'     => [
                        'pendingTime'           => 3600,
                        'sendLimit'             => 5,
                        'storeTime'             => 86400 * 7,
                        'expire'                => 3600,
                        'devEnvFilter'          => ['juks@juks.ru', 'ufokorpas@mail.ru', 'sergey.oracle@gmail.com', 'jeka.vinny@gmail.com']
                    ],

    'newsletter'  => [
                        'testList'              => ['juks@juks.ru', 'sergey.oracle@gmail.com']
                    ],

    'googleVision'  => [
                        'url' => 'https://vision.googleapis.com/v1/images:annotate',
                        'key' => 'AIzaSyChgzhOw14_QOl3ljtgberxkATtbdpZ81Q'
                    ]
];
