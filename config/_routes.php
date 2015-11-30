<?php

return [
    '/api/story/<id:\d+>'               => '/api/story/get',
    '/api/user/<id:\d+>'               	=> '/api/user/get',
    '/api/user/<username:[0-9a-z-]+>'   => '/api/user/get',
    '/sales/company'                    => '/sales/company/index',
    '/sales/company/view/<sID:\d+>'     => '/sales/company/view',
    '/sales/company/update/<sID:\d+>'   => '/sales/company/update',
    '/sales/company/delete/<sID:\d+>'   => '/sales/company/delete',
];

?>