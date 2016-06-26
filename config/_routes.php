<?php

return [
    '/api/story/<id:\d+>/?'               						    => '/api/story/get',
    ['pattern' =>'/api/story/<targetId:\d+>/comments/?',            'route' => '/api/comment/list-comments', 'defaults' => ['targetType' => 2]],
    '/api/media/<id:\d+>/?'                						    => '/api/media/get',
    ['pattern' =>'/api/media/<targetId:\d+>/comments/?',            'route' => '/api/comment/list-comments', 'defaults' => ['targetType' => 3]],
    '/api/media/<id:\d+>/like'            						    => '/api/media/like',
    '/api/media/<id:\d+>/unlike'           						    => '/api/media/unlike',
    '/api/user/profile/<id:\d+>'               					    => '/api/user/get',
    '/api/user/profile/<username:[0-9a-zA-Z-]{1,20}>'   		    => '/api/user/get',
    '/api/'                                                         => '/api/default/index',
    '/api-doc/'														=> '/api/default/doc',
    '/api/<controller:\w+>/<method:\w+>'						    => '/api/<controller>/<method>',

    '<alias:captcha|about|logout|help|howto>/?'					    => '/site/<alias>',

    '/<username:@?[0-9a-zA-Z-]{1,20}>/?'        					=> '/user-page/home',
    '/<username:@?[0-9a-zA-Z-]{1,20}>/story/<storyId:[0-9]{1,5}>/?'	=> '/user-page/story',
    '/<username:@?[0-9a-zA-Z-]{1,20}>/profile/?'					=> '/user-page/profile',
    '/<username:@?[0-9a-zA-Z-]{1,20}>/profile/edit/?'				=> '/user-page/edit',
    '/<username:@?[0-9a-zA-Z-]{1,20}>/feed/?'				        => '/user-page/feed',
];

?>