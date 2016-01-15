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
    'facebook'	=> [
						'appId' 	       => 301096096567708,
						'secretKey'        => 'c4bbbe8d885fc122a3928c3d51f3a6a4',
						'appToken' 	       => '301096096567708|TIAsRfbf4ixPj875rWQC6pbDdwc'
				   ],
    'mQueue'     => [
                        'pendingTime'      => 3600,
                        'sendLimit'        => 5,
                        'storeTime'        => 86400 * 7
                    ]
];
