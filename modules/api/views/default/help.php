<?php
    $api = [
                [
                    'title'                         => 'Auth',
                    'methods'                       => [
                        '/auth/login'               => [
                            'title' => 'Authenticates users',
                            'params'                => [
                                                            ['n' => 'username',     't' => 'Username'],
                                                            ['n' => 'password',     't' => 'User Password']
                                                    ]
                        ],
                    ]
                 ],

                [
                    'title'                         => 'Users',
                    'methods'                       => [
                        '/api/user/chek-username'   => [
                            'title' => 'Checks if given username is available',
                            'params'                => [
                                                            ['n' => 'username',     't' => 'Username'],
                                                    ]
                        ],

                        '/api/user/check-email'   => [
                            'title' => 'Checks if given email is available',
                            'params'                => [
                                                            ['n' => 'email',        't' => 'Preferred Email'],
                                                    ]
                        ],

                        '/api/user/register'   => [
                            'title' => 'Registers new user',
                            'params'                => [
                                                            ['n' => 'username',     't' => 'Preferred Username'],
                                                            ['n' => 'email',        't' => 'User Email'],
                                                            ['n' => 'password',     't' => 'User Password'],
                                                    ]
                        ],

                        '/api/user/update-profile'   => [
                            'title' => 'Updates user profile',
                            'auth'  => true,
                            'params'                => [
                                                            ['n' => 'id',           't' => 'User Id'],
                                                            ['n' => 'username',     't' => 'Preferred Username'],
                                                            ['n' => 'password',     't' => 'User Password'],
                                                            ['n' => 'email',        't' => 'User Email'],
                                                            ['n' => 'description',  't' => 'User Profile Description'],
                                                    ]
                        ]
                    ]
                 ],

                [
                    'title'                     => 'Stories',
                    'methods'                   => [
                        '/api/story/id'         => [
                            'title' => 'Fetches Story information',
                            'auth'  => true,
                        ],
                        '/api/story/write'      => [
                            'title' => 'Creates or updates story',
                            'auth'  => true,
                            'params'            => [
                                                            ['n' => 'id',           't' => 'Story Id',                      'h'=>'If not given, a new story will be created'],
                                                            ['n' => 'status',       't' => 'Story Status',                  'h'=>'0 — public, 1 — private'],
                                                            ['n' => 'title',        't' => 'Story Title'],
                                                            ['n' => 'description',  't' => 'Story Description'],
                                                    ]
                        ]
                    ]
                 ],

                [
                    'title'                     => 'Media',
                    'methods'                   => [
                        '/api/media/upload'     => [
                            'auth'  => true,
                            'title' => 'Uploads new media resorce',
                            'params'            => [
                                                            ['n' => 'targetId',     't' => 'Target Object Id',              'h'=>''],
                                                            ['n' => 'targetType',   't' => 'Target Object Type',            'h'=>'1 for user, 2 for story'],
                                                            ['n' => 'mediaType',    't' => 'Type of Uploaded Media',        'h'=>'eg. "userpic", "storyImage"'],
                                                            ['n' => 'date',         't' => 'Calendar data',                 'h'=>'Only for story images, eg. "2015-11-25"'],
                                                            ['n' => 'file',         't' => 'Media Resource',                'h'=>'eg. "userpic", "storyImage"'],
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
            width: 20%
        }

        td.middle {
            width: 40%;
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
            <td class="left"><a href="<?= $url ?>"><?=$url?></a></td><td class="middle"><?php if($method['title']): ?><?= $method['title'] ?><?php else: ?>&nbsp;<?php endif ?></td><td class="right">&nbsp;</td>
            </tr>
            <?php 
                if(!empty($method['auth'])) {
                    if (empty($method['params'])) $method['params'] = [];
                    $method['params'][] = ['n' => 'access-token',     't' => 'Access Token',              'h'=>''];
                }
            ?>
            <?php if (!empty($method['params'])): ?>
                <?php foreach ($method['params'] as $param): ?>
                    <tr class="content">
                        <td class="left">&nbsp;</td><td class="middle">?<?= $param['n'] ?></td><td class="right"><?= $param['t'] ?><?php if (!empty($param['h'])): ?> (<?= $param['h'] ?>)<?php endif ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
            </table>
        <?php endforeach ?>
<?php endforeach ?>

</body>
</html>