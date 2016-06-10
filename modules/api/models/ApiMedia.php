<?php

namespace app\modules\api\models;

use app\models\Media as BaseMedia;
use app\models\Story;
use app\components\traits\TLike;

class ApiMedia extends BaseMedia {
    use TLike;

    /**
    *   Sets the API scenarios
    **/    
    public function scenarios() {
        return [
            'default' => ['date', 'title', 'description']
        ];
    }

    /**
    *   Returns the form name where this model fields are set.
    *   In in case of api the entire objet root is okay
    **/
    public function formName() {
        return '';
    }

    /**
    *   Returns data for media player
    */
    public static function getPlayerData($params) {
        $isLeftEdge = false;
        $isRightEdge = false;

        $conditions = static::getActiveCondition();

        $conditions['target_id']    = $params['storyId'];
        $conditions['target_type']  = Story::typeId;

        if ($params['span'] > 0) {
            $conditions['date'] = ['<=', $params['date']];
            $order = '`date` DESC';
        } else {
            $conditions['date'] = ['>=', $params['date']];
            $order = '`date`';
        }

        $items = self::find()->where(self::makeCondition($conditions))->limit(abs($params['span']))->orderBy($order)->all();
        $cnt = count($items);
        
        foreach($items as $item) $item->setScenario('player');

        if ($cnt) {
            // Sort by date
            usort($items, function($a, $b) { if ($a['date'] == $b['date']) return 0; return (strtotime($a['date']) > strtotime($b['date'])) ? -1 : 1; });

            // Check edges
            $conditions['date'] = ['<', $items[$cnt - 1]['date']];
            if (!self::getCount($conditions)) $isRightEdge = true;
            $conditions['date'] = ['>', $items[0]['date']];
            if (!self::getCount($conditions)) $isLeftEdge = true;
        }

        return ['media' => $items, 'leftEdgeReached' => $isLeftEdge, 'rightEdgeReached' => $isRightEdge];
    }
}

?>