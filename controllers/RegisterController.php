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

    public function actionConfirm($id, $email, $code) {
        $this->setTitle(Ml::t('Password recovery'));
        $confirmError = false;

        $user = User::findOne($id);

        if (!$user) {
            $confirmError = 'Пользователь не найден';
        } elseif ($user->email != $email) {
            $confirmError = 'Адрес электронной почты изменился. Пожалуйста, воспользовайтесь обновлённой ссылкой из письма-уведомления.';
        } elseif (!$user->confirmEmail()) {
            $confirmError = 'Не удалось подтвердить адрес электронной почты';
        }

        return $this->render('confirm', ['confirmError' => $confirmError]);
    }

    public function actionRecover() {
        $this->setTitle(Ml::t('Password recovery'));

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
