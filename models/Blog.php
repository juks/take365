<?php

namespace app\models;

use Yii;
use app\models\base\BlogBase;
use app\models\Post;
use app\components\Helpers;
use app\components\interfaces\IPermissions;
use app\components\traits\THasPermission;
use app\components\traits\TModelExtra;

/**
 * Feed class
 */
class Blog extends BlogBase implements IPermissions {
	use TModelExtra;
    use THasPermission;

    const typeId = 4;

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
        }

        return parent::beforeValidate();
    }

    /**
     * Posts relation
     */
    public function getPosts() {
        if ($this->hasPermission(Yii::$app->user, IPermissions::permRead)) {
            $conditions = [
                                'is_published'  => 1
                            ];
            $order = 'time_published DESC';
        }

        return $this->hasMany(Post::className(), ['blog_id' => 'id'])->where($conditions)->orderBy($order);
    }

    /**
     * Fetch post by id
     */
    public static function getActivePost($id) {
        $post = Post::findOne($id);
        if (!$post) return null;

        $blog = Blog::findOne($post->blog_id);
        if (!$blog) return null;

        if ($blog->hasPermission(Yii::$app->user, IPermissions::permRead)) {
            return $post;
        } else {
            return null;
        }
    }
}