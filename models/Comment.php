<?php

namespace app\models;

use Yii;
use app\models\base\CommentBase;
use app\models\Story;
use app\components\Helpers;
use app\components\HelpersTxt;
use app\components\Ml;
use app\components\interfaces\IPermissions;
use app\components\traits\TModelExtra;
use app\components\traits\THasPermission;

/**
 * Feed class
 */
class Comment extends CommentBase {
	use TModelExtra;
	use THasPermission;

	const maxLevel = 7;

	public $urlTarget;

    /**
    *   Sets the scenarios
    **/    
    public function scenarios() {
        return [
            'default' => ['target_type', 'target_id', 'body']
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        $f =  [
            'id'        => 'id',
            'timestamp' => 'time_created',
            'body'    	=> 'body_jvx',
            'author'	=> 'author'
        ];

        return $f;
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created   = time();
            if (!$this->created_by) $this->created_by = Yii::$app->user->id;
        } else {
            $this->time_updated = time();
        }

        if (!$this->_oldAttributes['body'] !== $this->body) $this->body_jvx = HelpersTxt::simpleText($this->body);

        return parent::beforeValidate();
    }

    public static function deleteRecover($id) {
        $item = Comment::findOne($id);
        $user = Yii::$app->user;

        if (!$item) throw new \app\components\ModelException(Ml::t('Comment not found'));
        if (!$item->hasPermission($user, IPermissions::permWrite)) throw new app\components\ModelException(Ml::t('Comment not found'));

        if ($item->is_deleted) $item->is_deleted = false; else $item->is_deleted = true;
        $item->save();

        return $item->is_deleted;
    }

    /**
     * Returns owner id field name
     */
    public function getCreatorIdField() {
        return 'created_by';
    }

    /**
     * Returns public criteria
     */
	public function getIsPublic() {
        return $this->is_deleted == false;
    }

    /**
     * Author relation
     */
    public function getAuthor() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Returns comment URL (uses populated value of urlTarget)
     */
    public function getUrl() {
    	if ($this->urlTarget) return $this->urlTarget . '#comment' . $this->id; else return null;
    }

    public function getTarget() {
    	if ($this->target_type == Story::typeId) {
    		return Story::getActiveStory($this->target_id);
    	}
    }

    /**
     * Returns the criteria that indicates comment belongs to the current user
     */
    public function getIsMine() {
    	$user = Yii::$app->user;
    	return $this->created_by == $user->id;
    }

    /**
     * Returns the criteria that indicates comment belongs to the current user
     */
    public function getLevelLimited() {
    	return $this->level > self::maxLevel ? self::maxLevel : $this->level;
    }
}