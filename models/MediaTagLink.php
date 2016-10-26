<?php

namespace app\models;

use app\models\base\MediaTagLinkBase;

use Yii;
use app\models\Media;
use app\models\MediaTag;
use app\components\Helpers;
use app\components\traits\TModelExtra;

/**
 * Tag to media relation
 */
class MediaTagLink extends MediaTagLinkBase {
    use TModelExtra;

    /**
     *   Sets the lists of fields that are available for public exposure
     **/
    public function fields() {
        return [

        ];
    }

    /**
     *   Sets the Like model scenarios
     **/
    public function scenarios() {
        return [
            'default' => ['name']
        ];
    }

    /**
     * Prepare for validation
     */
    public function beforeValidate() {

        return parent::beforeValidate();
    }

    /**
     * Rebuilds the entire thing
     *
     * @throws \yii\db\Exception
     */
    public static function rebuild() {
        $tags = [];
        $tagsLink = [];
        $scoreLimit = 0.6;
        $tagCount = 0;

        foreach (Media::find()->where(['type' => Media::typeStoryImage, 'is_deleted' => 0, 'is_hidden' => 0])->with('annotation')->batch(100) as $items) {
            foreach ($items as $item) {
                $a = $item->getAnnotationArray('labelAnnotations');

                if ($a) {
                    $uniqueHash = [];
                    foreach ($a as $tag) {
                        if (!empty($uniqueHash[$tag->description]) || $tag->score < $scoreLimit) continue;

                        if (empty($tags[$tag->description])) {
                            $tagCount ++;
                            $tagId = $tagCount;
                            $tags[$tag->description] = ['id' => $tagId, 'description' => $tag->description, 'count' => 1];
                        } else {
                            $tagId = $tags[$tag->description]['id'];
                            $tags[$tag->description]['count'] ++;
                        }

                        $matchRatio = $tag->score < 1 ? intval($tag->score * 10)  : 1;

                        $tagsLink[] = ['tag_id' => $tagId, 'media_id' => $item->id, 'time_published' => $item->time_created, 'match' => $matchRatio];
                        $uniqueHash[$tag->description] = true;
                    }
                }
            }
        }

        $connection = Yii::$app->getDb();
        $connection->createCommand()->truncateTable('media_tag')->execute();
        $connection->createCommand()->truncateTable('media_tag_link')->execute();
        $connection->createCommand('SET UNIQUE_CHECKS = 0')->execute();

        self::insertArray('media_tag', ['id' => 'id', 'time_created' => ':unix_timestamp()', 'count' => 'count', 'name' => 'description'], $tags);
        self::insertArray('media_tag_link', ['tag_id' => 'tag_id', 'media_id' => 'media_id', 'time_published' => 'time_published', 'match' => 'match', 'is_active' => ':1'], $tagsLink);

        $connection->createCommand('SET UNIQUE_CHECKS = 1')->execute();
    }

    /**
     * Inserts array data into SQL
     *
     * @param $table
     * @param $columns
     * @param $data
     * @throws \yii\db\Exception
     */
    public static function insertArray($table, $columns, $data) {
        $connection = Yii::$app->getDb();
        $sqlColumnsString = '';
        $arrayColumns = [];

        foreach ($columns as $sqlName => $arrayName) {
            if ($sqlColumnsString) $sqlColumnsString .= ', ';
            $sqlColumnsString .= '`' . $sqlName . '`';
            $arrayColumns[] = $arrayName;
        }

        $insertCount = 0;
        $insertLimit = 50;
        $query = 'INSERT INTO ' . $table . ' (' . $sqlColumnsString . ')';
        $queryData = '';
        $i = 0;
        $count = count($data);

        foreach($data as $key => $value) {
            if ($queryData) $queryData .= ', ';
            $valuesString = '';

            foreach($arrayColumns as $columnName) {
                if ($valuesString) $valuesString .= ', ';
                $valuesString .= substr($columnName, 0, 1) == ':' ? substr($columnName, 1, strlen($columnName) - 1) : $connection->quoteValue($value[$columnName]);
            }
            $queryData .= '(' . $valuesString . ')';
            $insertCount ++;
            if ($insertCount == $insertLimit || $i == $count - 1) {
                $fullQuery = $query . ' values ' . $queryData;
                echo $fullQuery . "\n\n";
                $connection->createCommand($fullQuery)->execute();
                $insertCount = 0;
                $queryData = '';
            }

            $i++;
        }
    }

    public function getMedia() {
        return $this->hasOne(Media::className(), ['id' => 'media_id']);
    }

    public static function listByTag($name, $page = 1, $maxItems = 50) {
        $rewriteHash = [
                            'котэ'      => 'cat',
                            'гирла'     => 'girl',
                            'лисапед'   => 'bicycle'
                        ];

        $name = strtolower($name);
        if (!empty($rewriteHash[$name])) $name = $rewriteHash[$name];

        $page--;

        $tag = MediaTag::find()->where(['name' => $name])->one();

        if (!$tag) return null;

        return MediaTagLink::find()->where(['tag_id' => $tag->id])->with('media')->orderBy('`match` DESC, `time_published` DESC')->offset($page * $maxItems)->limit($maxItems)->all();
    }
}