<?php

namespace app\models;

use Yii;
use app\models\Story;
use app\models\Media;
use app\models\base\MosaicBase;
use app\components\Helpers;
use app\components\traits\TModelExtra;

/**
 * Mosaic class
 */
class Mosaic extends MosaicBase {
    use TModelExtra;

    const thumbLimit        = 500;
    const fileThumbLimit    = 50;
    const imageOffset       = 0;
    const storeTime         = 864000;

    /**
     * Prepare for validation
     */
    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!$this->time_created) $this->time_created = time();
        }

        return parent::beforeValidate();
    }

    /*
    * Mosaic image stripes generation
    */
    public function generate() {
        $storyIds = [28, 38, 42, 50, 115, 135, 159, 162, 183, 189, 194, 197, 203, 205, 240, 271, 273, 285, 295, 325, 336, 359, 389, 392, 416];
        $storyIds = Helpers::fetchFields(Story::sqlSelect('id', ['is_featured' => 1, 'status' => 0]), ['id'], ['isSingle' => true]);

        $storiesUrl = [];

        $cnt = count($storyIds);

        for($i = 0; $i < $cnt; $i++) {
            $story = Story::findOne($storyIds[$i]);

            if ($story->isPublic) {
                $storiesUrl[$story['id']] = $story->url;
            } else {
                unset($storyIds[$i]);
            }
        }

        $thumbDimension         = 200;
        $thumbTargetDimensions  = [140, 70];
        $thumbQuality           = 80;

        $mediaList = Media::find()->where(['target_id' => $storyIds, 'target_type' => Story::typeId, 'is_deleted' => false])->orderBy('id DESC')->limit(self::thumbLimit)->All();

        $sourceImage = new \Imagick();
        $data = '';

        $cnt = 0;
        $blockNum = 0;
        $imageCount = count($mediaList);
        $backgroundPixel = new \ImagickPixel('transparent');

        foreach($mediaList as $mediaItem) {
            $imagePath = $mediaItem->getThumbData(Media::resizeSquareCrop, $thumbDimension, ['path' => true])['path'];
            $cnt++;

            if (!file_exists($imagePath)) continue;

            if ($data) $data .= '|';
            $data .= $mediaItem['target_id'];

            $sourceImage->readImage($imagePath);
            $width = $sourceImage->getImageWidth();
            $height = $sourceImage->getImageHeight();
            $sourceImage->setBackgroundColor($backgroundPixel);
            $sourceImage->extentimage($width, $height + self::imageOffset, 0, 0);

            if ($cnt == self::fileThumbLimit || $cnt + $blockNum * self::fileThumbLimit == $imageCount) {
                $cnt = 0;
                $blockNum ++;

                $sourceImage->resetIterator();
                $wallpaperImage = $sourceImage->appendImages(true);

                foreach($thumbTargetDimensions as $thumbTargetDimension) {
                    $wallpaperImage->resizeimage($thumbTargetDimension, 0, \Imagick::FILTER_BLACKMAN, 0.80, false);

                    $outputPath = $this->getStorePath() . '/' . $this->id . '_' . $blockNum . '_' . $thumbTargetDimension . '.jpg';

                    $wallpaperImage->setImageFormat("jpg");
                    $wallpaperImage->setImageCompressionQuality($thumbQuality);
                    $wallpaperImage->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
                    $wallpaperImage->writeimage($outputPath);
                }

                $sourceImage->clear();
                $wallpaperImage->clear();
            }
        }

        $this->cleanup();
        $this->setAttributes(['is_ready' => 1, 'data' => $data]);
        $this->save();
    }

    public function getParsedData() {
        $storyIds = preg_split('/\|/', $this->data);
        $urls = [];

        $stories = Story::find()->where(['id' => array_unique($storyIds)])->all();

        foreach($stories as $story) {
            $urls[$story->id] = $story->url;
        }

        return ['urls' => $urls, 'ids' => $storyIds];
    }

    /*
    * Ooutdated mosaics removal
    */
    public function cleanup() {
        $total = $this->getCount();

        $killList = self::find()->where(['time_created' => ['<=', time() - self::storeTime]])->all();

        if (count($killList) >= $total) return;

        foreach($killList as $killItem) {
            foreach (glob($this->getStorePath() . $killItem->id . '_*') as $filename) {
                unlink($filename);
            }

            self::deleteAll(['id' => $killItem['id']]);
        }
    }

    /*
    * Returns current mosaic
    */
    public static function getCurrent() {
        return self::find()->where(['is_ready' => 1])->orderBy('id DESC')->one();
    }

    /*
    * Returns mosaic storage folder
    */
    function getStorePath() {
        return Yii::$app->params['mosaicPath'];
    }
}