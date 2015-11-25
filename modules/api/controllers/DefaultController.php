<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\api\components\ApiController;

class DefaultController extends ApiController
{
    public function actionIndex() {
        Yii::$app->response->format = Response::FORMAT_HTML;
        //Yii::$app->response->getHeaders()->add('Content-type', 'text/plain');

        $this->layout = false;
        $this->disableSend = true;
        return $this->render('help');
    }

    public function actionError() {
    	$e = Yii::$app->errorHandler->exception;
    	
    	$this->addErrorMessage($e->getMessage());

        if (YII_DEBUG) {
            $detailedMessage .= ' [' . $e->getFile() . ':' . $e->getLine() . ']';
            $this->addContent($detailedMessage, 'detailed');
            $traceData = '';

            foreach($e->getTrace() as $tracePoint) {
            	
                if (isset($tracePoint['file'])) {
                    $traceData = $tracePoint['file'] . ':';
                    $traceData .= $tracePoint['line'];
                }

                if (isset($tracePoint['function'])) $traceData . ' ' . $tracePoint['function'] . '()';
                $this->addContent($traceData, 'trace');
            }
        }
    }
}
