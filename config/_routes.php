<?php

return [
    '/api/story/<id:\d+>/?'               						    => '/api/story/get',
    ['pattern' =>'/api/story/<targetId:\d+>/comments/?',            'route' => '/api/comment/list-comments', 'defaults' => ['targetType' => 2]],
    ['pattern' =>'/api/story/<targetId:\d+>/write-comment/?',       'route' => '/api/comment/write', 'defaults' => ['targetType' => 2]],
    '/api/media/<id:\d+>/?'                						    => '/api/media/get',
    ['pattern' =>'/api/media/<targetId:\d+>/comments/?',            'route' => '/api/comment/list-comments', 'defaults' => ['targetType' => 3]],
    ['pattern' =>'/api/media/<targetId:\d+>/write-comment/?',       'route' => '/api/comment/write', 'defaults' => ['targetType' => 3]],
    '/api/media/<id:\d+>/like/?'           						    => '/api/media/like',
    '/api/media/<id:\d+>/unlike/?'         						    => '/api/media/unlike',
    '/api/media/<id:\d+>/likes/?'          						    => '/api/media/list-likes',
    '/api/user/profile/<id:\d+>'               					    => '/api/user/get',
    '/api/user/profile/<username:[0-9a-zA-Z-]{1,20}>'   		    => '/api/user/get',
    '/api/'                                                         => '/api/default/index',
    '/api-doc/'														=> '/api/default/doc',
    '/api/<controller:\w+>/<method:\w+>'						    => '/api/<controller>/<method>',
    ['pattern' =>'/help/<method:\s+>/?',                            'route' => '/help/<method>', 'defaults' => ['method' => 'index']],

    '<alias:captcha|about|logout|blog|tag|register>/?'              => '/site/<alias>',
    ['pattern' =>'/unsubscribe/<optionName:[a-zA-Z]+>/?',           'route' => '/site/unsubscribe'],
    '/blog/<id:\d+>/?'    		                                    => '/site/blog-post',

    '/<username:@?[A-Za-z0-9!][A-Za-z0-9\-._~\[\]@!$()*+=.]{0,19}>/?'        					                                    => '/user-page/home',
    '/<username:@?[A-Za-z0-9!][A-Za-z0-9\-._~\[\]@!$()*+=.]{0,19}>/story/<storyId:[0-9]{1,5}>/?'	                                => '/user-page/story',
    '/<username:@?[A-Za-z0-9!][A-Za-z0-9\-._~\[\]@!$()*+=.]{0,19}>/story/<storyId:[0-9]{1,5}>/<date:[0-9]{4}-[0-9]{2}-[0-9]{2}>/?'	=> '/user-page/story',
    '/<username:@?[A-Za-z0-9!][A-Za-z0-9\-._~\[\]@!$()*+=.]{0,19}>/profile/?'					                                    => '/user-page/profile',
    '/<username:@?[A-Za-z0-9!][A-Za-z0-9\-._~\[\]@!$()*+=.]{0,19}>/profile/edit/?'				                                    => '/user-page/edit',
    '/<username:@?[A-Za-z0-9!][A-Za-z0-9\-._~\[\]@!$()*+=.]{0,19}>/feed/?'				                                            => '/user-page/feed',
];

?>