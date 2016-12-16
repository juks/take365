<?php

namespace app\models;

use app\components\interfaces\IPermissions;
use Yii;
use app\models\base\PostBase;
use app\models\User;
use app\components\Helpers;
use app\components\HelpersTxt;
use app\components\traits\TModelExtra;
use app\components\traits\TComment;
use app\components\traits\TAuthor;

/**
 * Feed class
 */
class Post extends PostBase {
	use TModelExtra;
    use TComment;
    use TAuthor;

    const typeId = 5;

    /**
     *   Sets the Post model scenarios
     **/
    public function scenarios() {
        return [
            'default' => ['blog_id', 'is_published', 'title', 'body']
        ];
    }

    /**
     *   Sets the lists of fields that are available for public exposure
     **/
    public function fields() {
        $f =  [
            'id'             => 'id',
            'isPublished'    => 'is_published',
            'blogId'         => 'blog_id',
            'title'          => 'title',
            'body'           => function() { return $this->body_jvx; },
        ];

        return $f;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
            if ($this->is_published)  $this->time_published = time();
            if (!$this->created_by) $this->created_by = $this->created_by = Yii::$app->user->id;
        } else {
            $this->time_updated = time();
            if ($this->is_published && !$this->time_published) $this->time_published = time();
        }

        if (!$this->_oldAttributes['title'] !== $this->title) $this->title = strip_tags($this->title);
        if (!$this->_oldAttributes['body'] !== $this->body) $this->body_jvx = HelpersTxt::simpleText($this->body);

        return parent::beforeValidate();
    }

    /**
     * Returns public criteria
     */
    public function getIsPublic() {
        return $this->is_published == true;
    }

    /**
     * Checks the model permission
     *
     * @param object $user
     * @param int $permission
     **/
    public function checkPermission($user, $permission = IPermissions::permWrite) {
        $roles = \Yii::$app->authManager->getRolesByUser($user->id);
        if (!empty($roles['admin'])) return true;

        if ($permission == IPermissions::permRead && $this->getIsPublic()) return true;
        if ($permission == IPermissions::permComment && $this->getIsPublic()) return true;
        if ($this->created_by == $user->id) return true;
        if ($permission == IPermissions::permWrite && StoryCollaborator::hasPermission($this, $user)) return true;

        return false;
    }

    /**
     * Returns post with given ID only if it is available for current user
     *
     * @param int $postId
     **/
    public static function getActiveItem($postId) {
        $post = self::findOne($postId);
        $user = Yii::$app->user;

        if ($post) {
            if ($post->getIsPublic()) {
                return $post;
            } elseif ($post->hasPermission($user, IPermissions::permWrite)) {
                return $post;
            } else {
                return false;
            }
        }

        return null;
    }

    public function getCanManage() {
        $user = Yii::$app->user;

        return $this->checkPermission($user, IPermissions::permWrite);
    }

    public function getUrl() {
        return \yii\helpers\Url::base(true) . '/post/' . $this->id;
    }

    public function getUrlEdit() {
        return \yii\helpers\Url::base(true) . '/panel/post-write?id=' . $this->id;
    }
}