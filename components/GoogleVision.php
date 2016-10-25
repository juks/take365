<?php

namespace app\components;

use Yii;
use \app\components\Download;

class GoogleVision {
    public static function annotateImage($file) {
        $url = Helpers::getParam('googleVision/url') . '?key=' . Helpers::getParam('googleVision/key');

        $dl = new \app\components\Download();

        $request = [
            'requests' =>
                [[
                    'image' => ['content' => base64_encode(file_get_contents($file))],
                    'features' => [
                        [
                            'type' => 'LABEL_DETECTION',
                            'maxResults' => 20
                        ],

                        [
                            'type' => 'FACE_DETECTION',
                            'maxResults' => 10
                        ]
                    ]
                ]]
        ];

        $result = $dl->sendData($url, $request, ['isJson' => true, 'postJson' => true]);

        if ($result['code'] == 200) {
            return $result['data']->responses[0];
        } else {
            if (!empty($result['data']) && !empty($result['data']->error)) {
                throw new \Exception($result['data']->error->message);
            }
            return null;
        }
    }
}