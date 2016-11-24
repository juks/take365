<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\MQueue;
use app\components\Helpers;
use app\components\HelpersTxt;
use app\components\interfaces\IPermissions;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;

/**
 * Newsletter class
 */
class Newsletter extends \app\models\base\NewsletterBase implements IPermissions {
	use TModelExtra;
    use THasPermission;

    protected $_bodyJvx;

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

        $result = preg_replace('/%username%/i', $fullname, $this->bodyJvx);

        return $result;
    }

    /**
     * Delivers message for given user
     * @param $user
     * @throws \Exception
     */
    public function sendTo($user) {
        MQueue::compose()
            ->toUser($user)
            ->subject($this->title)
            ->bodyTemplate('newsletter.php', ['body' => $this->prepareUserBody(['user' => $user])])
            ->send();
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
        if ($this->time_sent) throw new \Exception('This message was already mass-delivered');

        foreach (User::find()->orderBy('id')->batch(100) as $users) {
            foreach ($users as $user) {
                //$this->sendTo($user);
            }
        }

        //$this->time_sent = time();
        //$this->save();
    }

    /**
     * Processes the body with Jevix
     * @return string
     */
    public function getBodyJvx() {
        if (!$this->_bodyJvx) {
            $this->_bodyJvx = HelpersTxt::simpleText($this->body);
        }

        return $this->_bodyJvx;
    }
}