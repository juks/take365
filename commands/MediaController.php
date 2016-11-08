<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Media;
use app\models\Mosaic;
use app\models\MediaTagLink;
use app\components\GoogleVision;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MediaController extends Controller {
    public $mediaType;
    public $resizeMode;
    public $targetDimension;
    public $doDelete = false;

    public function options($actionId) {
        $options = [
                        'make-thumbs'   => ['mediaType', 'resizeMode', 'targetDimension'],
                        'delete-thumbs' => ['mediaType', 'resizeMode', 'targetDimension'],
                    ];

        return !empty($options[$actionId]) ? $options[$actionId] : null;

    }

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionMosaic() {
        $m = new Mosaic();
        $m->save();
        $m->generate();
    }

    /**
     * Get media annotations and properties via Google Vision
     */
    public function actionAnnotate() {
        foreach (Media::find()->where(['type' => Media::typeStoryImage, 'is_annotated' => 0])->batch(100) as $items) {
            foreach ($items as $item) {
                $thumb = $item['t']['maxSide']['700'];
                $path = $thumb['path'];

                if (!file_exists($path)) {
                    echo 'File not found for #' . $item->id . "\n";
                    continue;
                }

                $data = GoogleVision::annotateImage($path);
                $item->setAnnotation($data, ['width' => $thumb['width'], 'height' => $thumb['height']]);

                echo $item->id . "\n";
            }
        }
    }

    public function actionMakeTags() {
        MediaTagLink::rebuild();
    }

    /**
     * Create thumbs of given type
     */
    public function actionMakeThumbs() {
        $typeId = Media::getTypeByAlias($this->mediaType);
        if (!$typeId) throw new \Exception('Invalid media type');
        if (!$this->resizeMode) throw new \Exception('No resize mode');
        if (!$this->targetDimension) throw new \Exception('No target dimension');

        foreach (Media::find()->where(['type' => $typeId, 'is_deleted' => 0])->orderBy('id')->batch(100) as $items) {
            foreach ($items as $item) {
                if (!file_exists($item->fullPath)) continue;

                $thumb = $item->makeThumb($this->resizeMode, $this->targetDimension, [media::forceThumbsCreate => true]);
                echo $thumb['path'] . "\n";
            }
        }
    }

    /*
     * Delete all files for given thumb type
     */
    public function actionDeleteThumbs() {
        $typeId = Media::getTypeByAlias($this->mediaType);
        if (!$typeId) throw new \Exception('Invalid media type');
        if (!$this->resizeMode) throw new \Exception('No resize mode');
        if (!$this->targetDimension) throw new \Exception('No target dimension');

        foreach (Media::find()->where(['type' => $typeId, 'is_deleted' => 0])->orderBy('id')->batch(100) as $items) {
            foreach ($items as $item) {
                $thumb = $item->makeThumb($this->resizeMode, $this->targetDimension);
                if (!file_exists($thumb['path']) || empty($thumb['resized'])) continue;

                if(!unlink($thumb['path'])) echo "Failed to delete " . $thumb['path'];
                echo $thumb['path'] . "\n";
            }
        }
    }

    public function actionPurgeDeleted() {
        Media::deleteMarked(500);
    }
}
