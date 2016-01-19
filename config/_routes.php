<?php

return [
    '/api/story/<id:\d+>'               						=> '/api/story/get',
    '/api/user/profile/<id:\d+>'               					=> '/api/user/get',
    '/api/user/profile/<username:[0-9a-z-]{1,20}}>'   			=> '/api/user/get',
    '/api/'														=> '/api/default/index',
    '/api/<controller:\w+>/<method:\w+>'						=> '/api/<controller>/<method>',

    '<alias:captcha|about|logout|help|howto>/?' 						=> '/site/<alias>',

    '/<username:[0-9a-z-]{1,20}>/?'								=> '/user-page/home',
    '/<username:[0-9a-z-]{1,20}>/story/<storyId:[0-9]{1,5}>/?'	=> '/user-page/story',
    '/<username:[0-9a-z-]{1,20}>/profile/?'						=> '/user-page/profile',
    '/<username:[0-9a-z-]{1,20}>/profile/edit/?'				        => '/user-page/edit',
];

?>