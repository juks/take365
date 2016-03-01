<?php
$api = [
            [
                'title'                         => 'Auth',

                'methods'                       => [
                    '/auth/login'               => [
                        'title' => 'Authenticates Users',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Username'],
                                                        ['n' => 'password',     't' => 'User Password']
                                                ]
                    ],

                    '/auth/logout'               => [
                        'title' => 'Forgets the web interface user',
                        'method' => 'POST',
                        'params'                => []
                    ],

                    '/auth/check-token'         => [
                        'title' => 'Checks Token Status',
                        'auth' => true,
                        'params'                => [
                                                ]
                    ],
                ]
             ],

            [
                'title'                         => 'Users',

                'methods'                       => [
                    '/user/profile/id'   => [
                        'title' => 'Retrieves User Profile Information',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Username or User Id',   'h' => 'Eg. "bob" for Bob or "1" for user with ID 1'],
                                                ]
                    ],

                    '/user/list'   => [
                        'title' => 'Fetches the list of users',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'page',         't' => 'Page Number',                   'o' => true],
                                                        ['n' => 'maxItems' ,    't' => 'Maximal Items Count',           'o' => true]
                                                ]
                    ],

                    '/user/check-username'   => [
                        'title' => 'Checks if given username is available',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Username'],
                                                ]
                    ],

                    '/user/check-email'   => [
                        'title' => 'Checks if given email is available',
                        'params'                => [
                                                        ['n' => 'email',        't' => 'Preferred Email'],
                                                ]
                    ],

                    '/user/register'   => [
                        'title' => 'Registers new user',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Preferred Username'],
                                                        ['n' => 'email',        't' => 'User Email'],
                                                        ['n' => 'password',     't' => 'User Password'],
                                                ]
                    ],

                    '/user/recover'   => [
                        'title' => 'Request password recovery',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'email',        't' => 'User Email'],
                                                ]
                    ],

                    '/user/recover-update'   => [
                        'title' => 'Update user password using recovery code',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'id',           't' => 'User Id'],
                                                        ['n' => 'code',         't' => 'Security Code'],
                                                        ['n' => 'password',     't' => 'New Password'],
                                                ]
                    ],

                    '/user/update-profile'   => [
                        'title' => 'Updates user profile',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'User Id'],
                                                        ['n' => 'username',     't' => 'Preferred Username',            'o' => true],
                                                        ['n' => 'fullname',     't' => 'Preferred Fullname',            'o' => true],
                                                        ['n' => 'password',     't' => 'User Password',                 'o' => true],
                                                        ['n' => 'email',        't' => 'User Email',                    'o' => true],
                                                        ['n' => 'description',  't' => 'User Profile Description',      'o' => true],
                                                ]
                    ],
                ]
             ],

            [
                'title'                     => 'Stories',

                'methods'                   => [
                    '/story/id'         => [
                        'title' => 'Fetches Story information',
                        'auth'  => true,
                    ],

                    '/story/list'   => [
                        'title' => 'Fetches the list of public or given user stories',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'page',         't' => 'Page Number',                       'o' => true],
                                                        ['n' => 'maxItems' ,    't' => 'Maximal Items Count',               'o' => true],
                                                        ['n' => 'username' ,    't' => 'Name of User to Fetch Stories of',  'o' => true,    'h' => 'Eg. "bob" for Bob or "me" for current user']
                                                ]
                    ],

                    '/story/write'      => [
                        'title' => 'Creates or updates story',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Story Id',                      'h'=>'If not given, a new story will be created'],
                                                        ['n' => 'status',       't' => 'Story Status',                  'h'=>'0 — public, 1 — private'],
                                                        ['n' => 'title',        't' => 'Story Title'],
                                                        ['n' => 'description',  't' => 'Story Description'],
                                                ]
                    ],

                    '/story/delete-recover'      => [
                        'title' => 'Deletes or recovers story',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Story Id',                      'h'=>'If not given, a new story will be created'],
                                                        ['n' => 'doRecover',    't' => 'Recover Deleted Items',         'h'=>'If set, the deleted items will be recovered'],
                                                ]
                    ],
                ]
             ],

            [
                'title'                     => 'Media',
                'methods'                   => [
                    '/media/player-data'     => [
                        'title' => 'Retrieves images for player',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'storyId',      't' => 'Story Id',                      'h'=>'1 for user, 2 for story'],
                                                        ['n' => 'date',         't' => 'Target Date',                   'h'=>''],
                                                        ['n' => 'span',         't' => 'Select Span',                   'h'=>'Eg. "-10 (left)", "10" (right)'],
                                                ]
                    ],

                    '/media/upload'     => [
                        'auth'  => true,
                        'title' => 'Uploads new media resource',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'targetId',     't' => 'Target Object Id',              'h'=>''],
                                                        ['n' => 'targetType',   't' => 'Target Object Type',            'h'=>'1 for user, 2 for story'],
                                                        ['n' => 'mediaType',    't' => 'Type of Uploaded Media',        'h'=>'Eg. "userpic", "storyImage"'],
                                                        ['n' => 'date',         't' => 'Calendar data',                 'h'=>'Only for story images, eg. "2015-11-25"'],
                                                        ['n' => 'file',         't' => 'Media Resource',                'h'=>'Eg. "userpic", "storyImage"'],
                                                ]
                    ],

                    '/media/write'     => [
                        'auth'  => true,
                        'title' => 'Updates media item\'s attributes',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Media Item Id',                'h'=>''],
                                                        ['n' => 'title',        't' => 'New Title',                    'h'=>''],
                                                        ['n' => 'description',  't' => 'New Description',              'h'=>''],
                                                ]
                    ],

                    '/media/swap-days'     => [
                        'auth'  => true,
                        'title' => 'Swaps the date of two story images',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'storyId',        't' => 'The story id'],
                                                        ['n' => 'dateA',          't' => 'The first item date',             'h'=>'Eg. "2015-05-20"'],
                                                        ['n' => 'dateB',          't' => 'The second item date',            'h'=>'Eg. "2015-05-15"'],
                                                ]
                    ],

                    '/media/delete-recover'     => [
                        'auth'  => true,
                        'title' => 'Deletes or recovers media items',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'idString',     't' => 'Media Items Identifiers',      'h'=>'Eg. "1,2,3"'],
                                                        ['n' => 'doRecover',    't' => 'Recover Deleted Items',        'h'=>'If set, the deleted items will be recovered'],
                                                ]
                    ],
                ]
             ],

            [
                'title'                     => 'Feed',
                'methods'                   => [
                    '/feed/feed'     => [
                        'auth'  => true,
                        'title' => 'Retrieves current user\'s feed',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'page',     't' => 'Page Number',               'h'=>'Eg. 1'],
                                                        ['n' => 'maxItems', 't' => 'Max Items Per Page',        'h'=>'Eg. 10 (max 100)'],
                                                ]
                    ],

                    '/feed/follow'     => [
                        'auth'  => true,
                        'title' => 'Follow user',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>''],
                                               ]
                    ],

                    '/feed/unfollow'     => [
                        'auth'  => true,
                        'title' => 'Unfollow user',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>''],
                                               ]
                    ],


                    '/feed/is-following'     => [
                        'auth'  => true,
                        'title' => 'Checks if current users follows other user',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>''],
                                               ]
                    ],
                ]
             ],

            [
                'title'                     => 'Collaboration',
                'methods'                   => [
                    '/collaborator/collaborators'     => [
                        'auth'  => true,
                        'title' => 'Retrieves story collaborators list',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'storyId',  't' => 'Story id'],
                                                ]
                    ],

                    '/collaborator/stories'     => [
                        'auth'  => true,
                        'title' => 'List stories that the given user listed as collaborator for',
                        'method' => 'GET',
                        'params'                => [
                                               ]
                    ],

                    '/collaborator/add'     => [
                        'auth'  => true,
                        'title' => 'Add story collaborator',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>''],
                                               ]
                    ],

                    '/collaborator/confirm'     => [
                        'auth'  => true,
                        'title' => 'Confirm pending collaboration',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>''],
                                               ]
                    ],

                    '/collaborator/remove'     => [
                        'auth'  => true,
                        'title' => 'Remove story collaborator',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>''],
                                               ]
                    ],
                ]
             ]
        ];

?>
<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        div.desc {
            padding-left: 150px;
        }

        h3.section {
            color: #333333;
        }

        table.reference {
            max-width: 1200px;
            width: 60%;
            padding-bottom: 20px;
        }

        table.reference tr td {
            vertical-align: top;
        }

        tr.header td {
            padding-bottom: 10px;
            font-size: 1em;
        }

        tr.header td.middle {
            color: #777777;
        }

        tr.content td {
            font-size: 0.9em;
        }

        td.left {
            width: 25%
        }

        td.middle {
            width: 35%;
        }

        td.right {
            font-style: italic;
            width: 40%;
        }
    </style>
</head>

<body>
<h2>Welcome to Take365 API 1.0!</h2>

<div>Available methods are:</div>
<?php foreach ($api as $section): ?>
    <h3 class="section">[<?= $section['title'] ?>]</h3>
        <?php foreach ($section['methods'] as $url => $method): ?>
            <table class="reference">
            <tr class="header">
            <td class="left"><?php if (!empty($method['method'])): ?><?= strtoupper($method['method']) ?><?php else: ?>GET<?php endif ?> <a href="/api<?= $url ?>">/api<?=$url?></a></td><td class="middle"><?php if (!empty($method['title'])): ?><?= $method['title'] ?><?php else: ?>&nbsp;<?php endif ?></td><td class="right">&nbsp;</td>
            </tr>
            <?php 
                if(!empty($method['auth'])) {
                    if (empty($method['params'])) $method['params'] = [];
                    $method['params'][] = ['n' => 'accessToken',     't' => 'Access Token',              'h'=>''];
                }
            ?>
            <?php if (!empty($method['params'])): ?>
                <?php foreach ($method['params'] as $param): ?>
                    <tr class="content">
                        <td class="left">&nbsp;</td><td class="middle">?<?php if (!empty($param['o'])) echo '?'; ?><?= $param['n'] ?></td><td class="right"><?= $param['t'] ?><?php if (!empty($param['h'])): ?> (<?= $param['h'] ?>)<?php endif ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
            </table>
        <?php endforeach ?>
<?php endforeach ?>

</body>
</html>