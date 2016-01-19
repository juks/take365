<?php

namespace app\controllers;

use Yii;
use app\models\RegisterForm;
use app\components\MyController;
use app\components\Ml;
use app\models\User;

class RegisterController extends MyController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionConfirm($id, $code) {
        $confirmError = false;

        $user = User::findOne($id);

        return $this->render('confirm', ['confirmError' => $confirmError]);
    }

    public function actionRecover() {
        $recoverError = false;
        $id = Yii::$app->request->getQueryParam('id');
        $code = Yii::$app->request->getQueryParam('code');

        if ($id && $code) {
            try {
                $user = User::findByPasswordResetToken($code, $id);
                if (!$user) $recoverError = 'Неправильный код безопасности!';
            } catch (\app\components\ModelException $e) {
                $recoverError = $e->getMessage();
            }

            return $this->render('recoverUpdate', ['recoverError' => $recoverError, 'id' => $id, 'code' => $code]);
        } else {
            return $this->render('recover');
        }
    }
}
