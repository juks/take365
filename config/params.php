<?php

return [
    'adminEmail' 					=> 'admin@example.com',
    'projectName'					=> 'Take365',
    'projectUrl'                    => 'http://take365.org/',
    'projectRobotEmail'             => 'noreply@take365.org',
    'mediaStorageQuota' 	 	    => 10737418240, // 10G
    'mediaFolderSpreadDepth'        => 1,
    'mediaFileClusterSize'          => 10000,
    'mediaThumbsAutoCreate'			=> false,
    'user'      => [
                        'recoveryLifetime' => 7200
                    ],
    'mQueue'     => [
                        'pendingTime'      => 3600,
                        'sendLimit'        => 5,
                        'storeTime'        => 86400 * 7,
                        'expire'           => 3600,
                        'devEnvFilter'     => ['juks@juks.ru', 'ufokorpas@mail.ru', 'sergey.oracle@gmail.com']
                    ]
];
