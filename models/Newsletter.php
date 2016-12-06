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
use yii\base\Exception;

/**
 * Newsletter class
 */
class Newsletter extends \app\models\base\NewsletterBase implements IPermissions {
	use TModelExtra;
    use THasPermission;

    protected $_attachKeysCache = null;

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

        if (1 || !$this->_oldAttributes['body'] !== $this->body) {
            $this->fetchResources();
        }

        return parent::beforeValidate();
    }

    /**
     * Looks message body for resources and replaces them
     */
    public function fetchResources() {
        $this->body = preg_replace_callback(
            '!(src="(https?://[^"]+)")!i',
            function($matches) {
                $url = $matches[2];

                $resource = Storage::getByKey(Storage::getKey($url));

                if (!$resource && Helpers::checkUrl($url)) {
                    $resource = new Storage();
                    $resource->takeFile($url);
                }

                if ($resource) {
                    return 'src="[[storage:' . $resource->key . ']]"';
                } else {
                    return $matches[1];
                }
            },
            $this->body
        );
    }

    /**
     * Retrieves the list of attachments keys
     * @return array
     */
    public function fetchAttachKeys() {
        if ($this->_attachKeysCache === null) {
            if (preg_match_all('/\[\[storage:([^]]+)\]\]/i', $this->body, $matches)) {
               $this->_attachKeysCache = $matches[1];
            } else {
                $this->_attachKeysCache = [];
            }
        }

        return $this->_attachKeysCache;
    }

    public function prepareUserBody($data) {
        $name = $data['user']->fullname ? \app\components\HelpersName::parseName($data['user']->fullname) : null;

        if(!$name) $name = 'уважаемый Пользователь';

        $result = preg_replace('/%username%/i', $name, $this->body);

        return $result;
    }

    /**
     * Delivers message for given user
     * @param $user
     * @throws \Exception
     */
    public function sendTo($user) {
        $attachments = $this->fetchAttachKeys();

        $m = MQueue::compose()
            ->toUser($user, ['checkOption' => 'newsletter'])
            ->from(Helpers::getParam('newsletterEmail'))
            ->subject($this->title)
            ->bodyTemplate('newsletter.php', ['body' => $this->prepareUserBody(['user' => $user])]);

        if ($attachments) {
            foreach ($attachments as $attachKey) {
                $resource = Storage::getByKey($attachKey);
                if ($resource) {
                    $m->attach($resource);
                } else {
                    throw new \Exception('Storage item not found!');
                }
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
        //
        // return;
        //
        if ($this->time_sent) throw new \app\components\ModelException('This message was already mass-delivered');

        try {
            foreach (User::find()->where(
                self::makeCondition(['email' => 'IS NOT NULL', 'email_confirmed' => true]))
                         ->orderBy('id')->batch(300) as $users) {
                foreach ($users as $user) {
                    $this->sendTo($user);
                }
            }
        } catch (\Exception $e) {
            $this->time_sent = time();
            $this->save();

            throw new \app\components\ControllerException('Delivery failed: ' . $e->getMessage());
        }

        $this->time_sent = time();
        $this->save();
    }
}