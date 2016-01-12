<?php

namespace app\modules\api\models;

use app\models\Media as BaseMedia;
use app\models\Story;

class ApiMedia extends BaseMedia {
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
        $isEdge = false;

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
            if ($params['span'] > 0) {
                $conditions['date'] = ['<', $items[$cnt - 1]['date']];
            } else {
                $conditions['date'] = ['>', $items[0]['date']];
            }
            
            if (!self::getCount($conditions)) $isEdge = true;
        }


        return ['media' => $items, 'edgeReached' => $isEdge];
    }
}

?>