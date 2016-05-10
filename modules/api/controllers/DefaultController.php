<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use app\modules\api\components\ApiController;
use app\modules\api\controllers\AuthController;

class DefaultController extends ApiController
{
    public function actionIndex() {
        Yii::$app->response->format = Response::FORMAT_HTML;

        $this->layout = false;
        $this->disableSend = true;
        return $this->render('index');
    }

    public function actionDoc() {
        Yii::$app->response->format = Response::FORMAT_HTML;

        $this->layout = false;
        $this->disableSend = true;
        return $this->render('swagger');        
    }

    public function actionError() {
    	$e = Yii::$app->errorHandler->exception;

        $params = [];

        // Somehow need to bind to set token error code
        if (get_class($e) == 'yii\web\UnauthorizedHttpException') $params['attributes'] = ['code' => AuthController::ERR_BAD_TOKEN];
    	
    	$this->addErrorMessage($e->getMessage(), $params);

        if (YII_DEBUG) {
            $detailedMessage = ' [' . $e->getFile() . ':' . $e->getLine() . ']';
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
