<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\MQueue;
use app\models\Storage;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;

/**
 * Newsletter class
 */
class Newsletter extends \app\models\base\NewsletterBase implements IPermissions {
	use TModelExtra;
    use THasPermission;

    public function getIsPublic() {
        return true;
    }

    public function getCreatorIdField() {
        return 'created_by';
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
            if (!$this->created_by) $this->created_by = $this->created_by = Yii::$app->user->id;
        } else {
            $this->time_updated = time();
        }

        return parent::beforeValidate();
    }

    public function prepareUserBody($data) {
        $fullname = $data['user']->fullnameFilled;
        if (substr($fullname, 0, 1) == '@') $fullname = 'Уважаемый пользователь';

        $result = preg_replace('/%username%/i', $fullname, $this->body);

        return $result;
    }

    /**
     * Delivers message for given user
     * @param $user
     * @throws \Exception
     */
    public function sendTo($user) {
        $attachments = [1];

        $m = MQueue::compose()
            ->toUser($user, ['checkOption' => 'newsletter'])
            ->from(Helpers::getParam('newsletterEmail'))
            ->subject($this->title)
            ->bodyTemplate('newsletter.php', ['body' => $this->prepareUserBody(['user' => $user])]);

        if ($attachments) {
            foreach ($attachments as $attachId) {
                $m->attach(Storage::findOne($attachId));
            }
        }

        $m->send();
    }

    /**
     * Performs test delivery
     * @throws \Exception
     */
    public function testDeliver() {
        $emails = Helpers::getParam('newsletter/testList');
        if (!$emails || !is_array($emails)) throw new \Exception('No test email list');

        foreach ($emails as $email) {
            $user = User::find()->where(['email' => $email])->one();

            if ($user) {
                $this->sendTo($user);
            }
        }
    }

    /**
     * Performs mass delivery (CAUTION!)
     * @throws \Exception
     */
    public function massDeliver() {
        //$emails = Helpers::getParam('newsletter/testList');
        if ($this->time_sent) throw new \app\components\ModelException('This message was already mass-delivered');

        foreach (User::find()->orderBy('id')->batch(100) as $users) {
            foreach ($users as $user) {
                //$this->sendTo($user);
            }
        }

        //$this->time_sent = time();
        //$this->save();
    }
}