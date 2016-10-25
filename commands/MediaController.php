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
use app\components\GoogleVision;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MediaController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionMosaic() {
        $m = new Mosaic();
        $m->save();
        $m->generate();
    }

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
}
