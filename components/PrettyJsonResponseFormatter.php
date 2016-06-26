<?php

namespace app\components;

use yii\helpers\Json;

class PrettyJsonResponseFormatter extends \yii\web\JsonResponseFormatter {
    
    protected function formatJson($response) {

        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        if ($response->data !== null) {
        	if (YII_DEBUG) {
            	$response->content = Json::encode($response->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
            	$response->content = Json::encode($response->data, JSON_UNESCAPED_UNICODE);
            }
        }
    }
}