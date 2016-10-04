<?php

namespace app\models;

use Yii;
use app\models\base\PostBase;
use app\components\Helpers;
use app\components\traits\TModelExtra;
use app\components\traits\TComment;

/**
 * Feed class
 */
class Post extends PostBase {
	use TModelExtra;
    use TComment;

    const typeId = 5;

    /**
     *   Sets the Story model scenarios
     **/
    public function scenarios() {
        return [
            'default' => ['blog_id', 'is_published', 'title', 'story']
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
        }

        if (!$this->_oldAttributes['title'] !== $this->title) $this->title = strip_tags($this->title);
        if (!$this->_oldAttributes['body'] !== $this->body) $this->body_jvx = HelpersTxt::simpleText($this->body);

        return parent::beforeValidate();
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
            if ($post->is_published) {
                return $post;
            } elseif ($post->hasPermission($user, IPermissions::permWrite)) {
                return $post;
            }
        }

        return null;
    }
}