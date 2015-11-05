<?php

return [
    '/api/story/<id:\d+>'                     	=> '/api/story/get',
    '/sales/company'                    => '/sales/company/index',
    '/sales/company/view/<sID:\d+>'     => '/sales/company/view',
    '/sales/company/update/<sID:\d+>'   => '/sales/company/update',
    '/sales/company/delete/<sID:\d+>'   => '/sales/company/delete',
];

?>