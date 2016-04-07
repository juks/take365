<?php
$host = \yii\helpers\Url::base(true);
$schema = substr($host, 0, 7) == 'http://' ? 'http://' : 'https://';
$host = preg_replace('!https?://!', '', $host);

$api = [
            [
                'title'                         => 'Auth',
                'description'                   => 'Users authentication is done using this method',
                'methods'                       => [
                    '/auth/login'               => [
                        'title' => 'Authenticates Users',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Username', 'f' => 'string'],
                                                        ['n' => 'password',     't' => 'User Password', 'f' => 'string']
                                                ],
                        'responses'             => ['200' => ['s' => 'Token']]
                    ],

                    '/auth/logout'               => [
                        'title' => 'Forgets the Web Interface User',
                        'method' => 'POST',
                        'params'                => [],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/auth/check-token'         => [
                        'title' => 'Checks Token Status',
                        'method' => 'GET',
                        'auth' => true,
                        'params'                => [],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],
                ]
             ],

            [
                'title'                         => 'Users',
                'description'                   => 'Entire user profile related stuff.',
                'methods'                       => [
                    '/user/profile/{id}'   => [
                        'title' => 'Retrieves User Profile Information',
                        'method' => 'GET',
                        'auth'  => true,
                        'params'                => [['n' => 'id',           't' => 'Username or User Id',   'h' => 'Eg. "bob" for Bob or "1" for user with ID 1', 'f' => 'integer', 'in' => 'path']],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],

                    '/user/list'   => [
                        'title' => 'Fetches the List of Users',
                        'method' => 'GET',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'page',         't' => 'Page Number',                   'o' => true, 'f' => 'integer'],
                                                        ['n' => 'maxItems' ,    't' => 'Maximal Items Count',           'o' => true, 'f' => 'integer']
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'User']]
                    ],

                    '/user/suggest'   => [
                        'title' => 'Gives Users Suggest for Given Username Part',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Username', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'User']]
                    ],

                    '/user/check-username'   => [
                        'title' => 'Checks If Given Username is Available',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Username', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/check-email'   => [
                        'title' => 'Checks if Given Email is Available',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'email',        't' => 'Preferred Email', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],


                    '/user/register'   => [
                        'title' => 'Registers New User',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',     't' => 'Preferred Username', 'f' => 'string'],
                                                        ['n' => 'email',        't' => 'User Email', 'f' => 'string'],
                                                        ['n' => 'password',     't' => 'User Password', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],

                    '/user/recover'   => [
                        'title' => 'Request Password Recovery',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'email',        't' => 'User Email', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/recover-update'   => [
                        'title' => 'Update User Password Using Recovery Code',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'id',           't' => 'User Id', 'f' => 'integer'],
                                                        ['n' => 'code',         't' => 'Security Code', 'f' => 'string'],
                                                        ['n' => 'password',     't' => 'New Password', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/update-profile'   => [
                        'title' => 'Updates User Profile',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'User Id', 'f' => 'integer'],
                                                        ['n' => 'username',     't' => 'Preferred Username',            'o' => true, 'f' => 'string'],
                                                        ['n' => 'fullname',     't' => 'Preferred Fullname',            'o' => true, 'f' => 'string'],
                                                        ['n' => 'password',     't' => 'User Password',                 'o' => true, 'f' => 'string'],
                                                        ['n' => 'email',        't' => 'User Email',                    'o' => true, 'f' => 'string'],
                                                        ['n' => 'description',  't' => 'User Profile Description',      'o' => true, 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'User']]
                    ],

                    '/user/set-options'   => [
                        'title' => 'Set User Options',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'options',      't' => 'Options name-value array', 'f' => 'array'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/user/get-option'   => [
                        'title' => 'Get User Option Value',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'name',         't' => 'Option name', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],
                ]
             ],

            [
                'title'                     => 'Stories',
                'description'               => 'Provides read and write access to stories data.',
                'methods'                   => [
                    '/story/{id}'         => [
                        'title' => 'Fetches Story information',
                        'method' => 'GET',
                        'auth'  => true,
                        'params'                =>      [['n' => 'id',           't' => 'Story Id', 'f' => 'integer', 'in' => 'path']],
                        'responses'             => ['200' => ['s' => 'Story']]
                    ],

                    '/story/list'   => [
                        'title' => 'Fetches the list of public or given user stories',
                        'method' => 'GET',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'page',         't' => 'Page Number',                       'o' => true, 'f' => 'integer'],
                                                        ['n' => 'maxItems' ,    't' => 'Maximal Items Count',               'o' => true, 'f' => 'integer'],
                                                        ['n' => 'username' ,    't' => 'Name of User to Fetch Stories of',  'o' => true,    'h' => 'Eg. "bob" for Bob or "me" for current user', 'f' => 'string']
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'Story']]
                    ],

                    '/story/write'      => [
                        'title' => 'Creates or updates story',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Story Id',                      'h'=>'If not given, a new story will be created', 'f' => 'integer'],
                                                        ['n' => 'status',       't' => 'Story Status',                  'h'=>'0 — public, 1 — private', 'f' => 'integer'],
                                                        ['n' => 'title',        't' => 'Story Title', 'f' => 'string'],
                                                        ['n' => 'description',  't' => 'Story Description', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Story']]
                    ],

                    '/story/delete-recover'      => [
                        'title' => 'Deletes or recovers story',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Story Id',                      'h'=>'If not given, a new story will be created', 'f' => 'integer'],
                                                        ['n' => 'doRecover',    't' => 'Recover Deleted Items',         'h'=>'If set, the deleted items will be recovered', 'f' => 'boolean'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],
                ]
             ],

            [
                'title'                     => 'Comments',
                'description'               => 'Comments are easy thing to do.',
                'methods'                   => [
                    '/comment/list-comments'   => [
                        'title' => 'Retrieve target comments',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'targetType',   't' => 'Commentable Target Type', 'f' => 'integer'],
                                                        ['n' => 'targetId' ,    't' => 'Commentable Id', 'f' => 'integer'],
                                                        ['n' => 'lastTimestamp','t' => 'Show only comments that were created since given timestamp', 'o' => true, 'f' => 'integer'],
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'Comment']]
                    ],

                    '/comment/write'   => [
                        'title' => 'Creates or updates comment',
                        'method' => 'POST',
                        'auth'  => true,
                        'params'                => [
                                                        ['n' => 'targetType',   't' => 'Commentable Target Type',      'o' => true, 'f' => 'integer'],
                                                        ['n' => 'targetId' ,    't' => 'Commentable Id',               'o' => true, 'f' => 'integer'],
                                                        ['n' => 'id' ,          't' => 'Comment Id To Update',         'o' => true, 'f' => 'integer'],
                                                        ['n' => 'body' ,        't' => 'Comment Text',                 'h' => '2 for story', 'f' => 'string'],
                                                        ['n' => 'parentId' ,    't' => 'Parent Comment Id',            'o' => true, 'h' => '2 for story', 'f' => 'integer'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Comment']]
                    ],

                    '/comment/delete-recover'     => [
                        'auth'  => true,
                        'title' => 'Deletes or recovers comments',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Comment Id', 'f' => 'integer']
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],
                ]
             ],

            [
                'title'                     => 'Media',
                'description'               => 'Image upload and control methods.',
                'methods'                   => [
                    '/media/player-data'     => [
                        'title' => 'Retrieves images for player',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'storyId',      't' => 'Story Id',                      'h'=>'1 for user, 2 for story', 'f' => 'integer'],
                                                        ['n' => 'date',         't' => 'Target Date',                   'h'=>'', 'f' => 'string'],
                                                        ['n' => 'span',         't' => 'Select Span',                   'h'=>'Eg. "-10 (left)", "10" (right)', 'f' => 'integer'],
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'Media']]
                    ],

                    '/media/upload'     => [
                        'auth'  => true,
                        'isMultipart' => true,
                        'title' => 'Uploads new media resource',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'targetId',     't' => 'Target Object Id',              'h'=>'', 'f' => 'integer'],
                                                        ['n' => 'targetType',   't' => 'Target Object Type',            'h'=>'1 for user, 2 for story', 'f' => 'integer'],
                                                        ['n' => 'mediaType',    't' => 'Type of Uploaded Media',        'h'=>'Eg. "userpic", "storyImage"', 'f' => 'string'],
                                                        ['n' => 'date',         't' => 'Calendar data',                 'h'=>'Only for story images, eg. "2015-11-25"', 'f' => 'string'],
                                                        ['n' => 'file',         't' => 'Media Resource',                'h'=>'Eg. "userpic", "storyImage"', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Media']]
                    ],

                    '/media/write'     => [
                        'auth'  => true,
                        'title' => 'Updates media item\'s attributes',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'id',           't' => 'Media Item Id',                'h'=>'', 'f' => 'integer'],
                                                        ['n' => 'title',        't' => 'New Title',                    'h'=>'', 'f' => 'string'],
                                                        ['n' => 'description',  't' => 'New Description',              'h'=>'', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Media']]
                    ],

                    '/media/swap-days'     => [
                        'auth'  => true,
                        'title' => 'Swaps the date of two story images',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'storyId',        't' => 'The story id', 'f' => 'integer'],
                                                        ['n' => 'dateA',          't' => 'The first item date',             'h'=>'Eg. "2015-05-20"', 'f' => 'string'],
                                                        ['n' => 'dateB',          't' => 'The second item date',            'h'=>'Eg. "2015-05-15"', 'f' => 'string'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/media/delete-recover'     => [
                        'auth'  => true,
                        'title' => 'Deletes or recovers media items',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'idString',     't' => 'Media Items Identifiers',      'h'=>'Eg. "1,2,3"', 'f' => 'string'],
                                                        ['n' => 'doRecover',    't' => 'Recover Deleted Items',        'h'=>'If set, the deleted items will be recovered', 'f' => 'boolean'],
                                                ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],
                ]
             ],

            [
                'title'                     => 'Feed',
                'description'               => 'The feed of images, uploaded by those current user is subscribed for.',
                'methods'                   => [
                    '/feed/feed'     => [
                        'auth'  => true,
                        'title' => 'Retrieves current user\'s feed',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'page',     't' => 'Page Number',               'h'=>'Eg. 1', 'f' => 'integer'],
                                                        ['n' => 'maxItems', 't' => 'Max Items Per Page',        'h'=>'Eg. 10 (max 100)', 'f' => 'integer'],
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'Media']]
                    ],

                    '/feed/follow'     => [
                        'auth'  => true,
                        'title' => 'Follow user',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>'', 'f' => 'string'],
                                               ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/feed/unfollow'     => [
                        'auth'  => true,
                        'title' => 'Unfollow user',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>'', 'f' => 'string'],
                                               ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],


                    '/feed/is-following'     => [
                        'auth'  => true,
                        'title' => 'Checks if current users follows other user',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>'', 'f' => 'string'],
                                               ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],
                ]
             ],

            [
                'title'                     => 'Collaboration',
                'description'               => 'Collaboration lets multiple users to work on one story',
                'methods'                   => [
                    '/collaborator/collaborators'     => [
                        'auth'  => true,
                        'title' => 'Retrieves story collaborators list',
                        'method' => 'GET',
                        'params'                => [
                                                        ['n' => 'storyId',  't' => 'Story id', 'f' => 'integer'],
                                                ],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'User']]
                    ],

                    '/collaborator/stories'     => [
                        'auth'  => true,
                        'title' => 'List stories that the given user listed as collaborator for',
                        'method' => 'GET',
                        'params'                => [],
                        'responses'             => ['200' => ['t' => 'array', 's' => 'Story']]
                    ],

                    '/collaborator/add'     => [
                        'auth'  => true,
                        'title' => 'Add story collaborator',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>'', 'f' => 'string'],
                                               ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/collaborator/confirm'     => [
                        'auth'  => true,
                        'title' => 'Confirm pending collaboration',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>'', 'f' => 'string'],
                                               ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],

                    '/collaborator/remove'     => [
                        'auth'  => true,
                        'title' => 'Remove story collaborator',
                        'method' => 'POST',
                        'params'                => [
                                                        ['n' => 'username',      't' => 'Username or user id',              'h'=>'', 'f' => 'string'],
                                               ],
                        'responses'             => ['200' => ['s' => 'Response']]
                    ],
                ]
             ]
        ];
?>
swagger: '2.0'
info:
  title: Take365 API
  description: Please see the <?= $host ?> public API description below.
  version: "1.0"
# the domain of the service
host: <?= $host ?> 
# array of all schemes that your API supports
schemes:
  - http
# will be prefixed to all paths
basePath: /api
produces:
  - application/json
securityDefinitions:
  internalApiKey:
    type: apiKey
    name: accessToken
    in: header
paths:
<?php
    foreach ($api as $group) {
        foreach ($group['methods'] as $name => $data) {
            echo '  ' . $name . ":\n";
            echo '    ' . strtolower($data['method']) . ":\n";
            echo '      summary: ' . $data['title'] . "\n";
            echo '      tags: ' . "\n";
            echo '        - ' . $group['title'] . "\n";
            echo '      consumes: ' . "\n";
            if (empty($data['isMultipart'])) {
                echo '        - application/x-www-form-urlencoded' . "\n";
            } else {
                echo '        - multipart/form-data' . "\n";
            } 

           /**
            * Parameters
            **/
            if (!empty($data['params'])) {
                echo '      parameters:' . "\n";

                //if (!empty($data['auth']))
                //    if (!isset($data['params'])) $data['params'] = [];
                //    if (!empty($data['auth'])) $data['params'][] = ['n' => 'access-token',      't' => 'Access Token', 'f' => 'string'];

                foreach ($data['params'] as $param) {
                    echo '        - name: ' . $param['n'] . "\n";
                    echo '          in: ';
                    if (!empty($param['in'])) echo $param['in']; else echo (strtolower($data['method']) == 'get' ? 'query' : 'formData');
                    echo "\n";
                    echo '          description: ' . $param['t'] . "\n";
                    echo '          required: ' . (empty($param['o']) ? 'true' : 'false') . "\n";
                    echo '          type: ' . ($param['f'] == 'integer' ? 'integer' : 'string') . "\n";
                    if ($param['f'] == 'integer') echo '          format: int32' . "\n";
                }
            }

            /**
            * Responses
            **/
            if (!empty($data['responses'])) {
                echo '      responses:' . "\n";
                foreach ($data['responses'] as $code => $responseData) {
                    echo '        "' . $code . '":' . "\n";
                    echo '          description: ' . ($code == 200 ? 'OK' : 'Not Acceptable') . "\n";
                    echo '          schema: ' . "\n";
                    if (!empty($responseData['t']) && $responseData['t'] == 'array') {
                        echo '            type: array' . "\n";
                        echo '            items:' . "\n";
                        echo '              $ref: \'#/definitions/' . $responseData['s'] . "'\n";
                    } else {
                        echo '            $ref: \'#/definitions/' . $responseData['s'] . "'\n";
                    }
                }
            }
        } 
    }
?>
tags:
  - name: Auth
    description: |
      Users authentication is done using this method
  - name: Users
    description: |
      Entire user profile related stuff
  - name: Stories
    description: |
      Provides read and write access to stories data
  - name: Comments
    description: |
      Comments are easy thing to do
  - name: Media
    description: |
      Image upload and control methods
  - name: Feed
    description: |
      User feed access
  - name: Collaboration
    description: |
      Collaborative story authorship
definitions:
  Response:
    type: object
    properties:
      result:
        type: string
        description: Result message    
  Token:
    type: object
    properties:
      id:
        type: integer
        description: User id
      username:
        type: string
        description: Username
      token:
        type: string
        description: Access token value
      tokenExpires:
        type: integer
        description: Token expiration timestamp
  User:
    type: object
    properties:
      id:
        type: integer
        description: User id
      username:
        type: string
        description: Username
      url:
        type: string
        description: User page URL
      userpic:
        type: string
        description: User image URL
      userpicLarge:
        type: string
        description: Large user image URL
  Story:
    type: object
    properties:
      id:
        type: integer
        description: Story id
      status:
        type: integer
        description: Story status
      title:
        type: string
        description: Story title
      url:
        type: string
        description: The story URL
      authors:
        type: array
        description: The list of story authors
        items: 
          $ref: '#/definitions/User'
      progress:
        type: object
        description: Story progress information
  Media:
    type: object
    properties:
      id:
        type: integer
        description: Media item id
      title:
        type: string
        description: Media title
      description:
        type: string
        description: Media description
      thumb:
        type: string
        description: Thumb image URL
      thumbLarge:
        type: string
        description: Large thumb image URL
      story:
        type: object
        description: Target story
      date:
        type: string
        description: The calendar date (for story image)
  Comment:
    type: object
    properties:
      id:
        type: integer
        description: Comment id
      isDeleted:
        type: boolean
        description: Deleted comment flag
      level:
        type: integer
        description: Comment nest level
      thread:
        type: integer
        description: Comment root thread id
      timestamp:
        type: integer
        description: Creation timestamp
      body:
        type: string
        description: Comment body
      author:
        type: object
        description: Comment creator