<?php
$host = \yii\helpers\Url::base(true);
$schema = substr($host, 0, 7) == 'http://' ? 'http://' : 'https://';
$host = preg_replace('!https?://!', '', $host);

$fetchList = [
                '\app\modules\api\controllers\AuthController',
                '\app\modules\api\controllers\UserController',
                '\app\modules\api\controllers\StoryController',
                '\app\modules\api\controllers\CommentController',
                '\app\modules\api\controllers\MediaController',
                '\app\modules\api\controllers\FeedController',
                '\app\modules\api\controllers\CollaboratorController',
             ];

$api = [];

foreach ($fetchList as $controllerName) {
    $api[] = call_user_func($controllerName . '::getSwaggerData');
}

?>
swagger: '2.0'
info:
  title: Take365 API
  description: |
    Have a look at <a href="<?= $schema ?><?= $host ?>"><?= $host ?></a> public API description below. To obtain the auth token use the auth/login method then copy the value into the form above.
  version: "1.0"
# the domain of the service
host: <?= $host ?> 
# array of all schemes that your API supports
schemes:
  - https
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

                foreach ($data['params'] as $param) {
                    echo '        - name: ' . $param['n'] . "\n";
                    echo '          in: ';
                    if (!empty($param['in'])) echo $param['in']; else echo (strtolower($data['method']) == 'get' ? 'query' : 'formData');
                    echo "\n";
                    echo '          description: ' . $param['t'] . "\n";
                    echo '          required: ' . (empty($param['o']) ? 'true' : 'false') . "\n";
                    echo '          type: ' . ($param['f'] == 'integer' || $param['f'] == 'file' ? $param['f'] : 'string') . "\n";
                    // Format
                    if ($param['f'] == 'integer') echo '          format: int32' . "\n";
                    // Enum
                    if (!empty($param['e'])) {
                        echo '          enum:' . "\n";
                        foreach($param['e'] as $value) {
                            echo '            - ' . $value . "\n";
                        }
                    }
                    // Default
                    if (!empty($param['d'])) {
                        echo '          default: ' . (is_string($param['d']) ? '\'' . $param['d'] . '\'' : $param['d']). "\n";
                    }
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