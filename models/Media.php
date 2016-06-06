<?php

namespace app\models;

use Yii;
use app\models\mediaExtra\MediaCore;
use app\models\User;
use app\models\Story;

/**
 * Story class
 */
class Media extends MediaCore {
    const typeUserpic       = 1;
    const aliasUserpic      = 'userpic';
    const typeStoryImage    = 2;
    const aliasStoryImage   = 'storyImage';

    protected static $_globalOptions = [
                                    Media::typeUserpic => [
                                                        MediaCore::typeId                => 1,
                                                        MediaCore::targetType            => 1,
                                                        MediaCore::alias                 => Media::aliasUserpic,
                                                        MediaCore::cleanPrev             => true,
                                                        MediaCore::allowedFormats        => [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG],
                                                        MediaCore::maxFileSize           => 1048576 * 10,
                                                        MediaCore::resizeMode            => MediaCore::resizeMaxSide,
                                                        MediaCore::targetDimension       => 3000,
                                                        MediaCore::thumbsList            => [
                                                                                                MediaCore::resizeMaxSide => [500, 200, 100, 50],
                                                                                            ],
                                                        MediaCore::mainThumbDimension    => 500,
                                                        MediaCore::largeThumbDimension   => 500,
                                                        MediaCore::quality               => 95,
                                                        MediaCore::engine                => MediaCore::engineImageMagick,
                                                        MediaCore::resizeFilter          => \Imagick::FILTER_BLACKMAN,
                                                        MediaCore::resizeBlur            => 0.86,
                                                        MediaCore::thumbQuality          => 96,
                                                        MediaCore::saveExif              => true,
                                                        MediaCore::autoOrient            => true
                                                ],

                                    Media::typeStoryImage => [
                                                        MediaCore::typeId                => 2,
                                                        MediaCore::targetType            => 2,
                                                        MediaCore::alias                 => Media::aliasStoryImage,
                                                        MediaCore::cleanPrev             => true,
                                                        MediaCore::allowedFormats        => [IMAGETYPE_JPEG, IMAGETYPE_PNG],
                                                        MediaCore::maxFileSize           => 1048576 * 10,
                                                        MediaCore::resizeMode            => MediaCore::resizeMaxSide,
                                                        MediaCore::targetDimension       => 3000,
                                                        MediaCore::thumbsList            => [
                                                                                                MediaCore::resizeMaxSide    => [1400, 700, 200, 100],
                                                                                                MediaCore::resizeSquareCrop => [400, 200, 100, 50],
                                                                                        ],

                                                        MediaCore::mainThumbDimension    => 200,
                                                        MediaCore::largeThumbDimension   => 400,
                                                        MediaCore::quality               => 95,
                                                        MediaCore::engine                => MediaCore::engineImageMagick,
                                                        MediaCore::resizeFilter          => \Imagick::FILTER_BLACKMAN,
                                                        MediaCore::resizeBlur            => 0.86,
                                                        MediaCore::thumbQuality          => 96,
                                                        MediaCore::saveExif              => true,
                                                        MediaCore::autoOrient            => true,
                                                        MediaCore::resizeScaleUpDimension=> 400
                                                    ]
                                ];


    /**
    *   Sets the Media model scenarios
    **/
    public function scenarios() {
        return [
            'import' => ['id', 'target_id', 'filename', 'ext', 'target_type', 'type', 'id_old', 'date', 'is_deleted', 'time_created', 'title', 'description', 'description_jvx', 'created_by']
        ];
    }

    /**
    *   Sets the lists of fields that are available for public exposure
    **/
    public function fields() {
        $fields = [
                        'id'            => 'id',
                        'title'         => 'title',
                        'description'   => 'description_jvx'
                    ];

        if ($this->scenario == 'player') {
            $fields['thumb']         = function() { return $this->getThumbData(MediaCore::resizeMaxSide, 700); };
            $fields['thumbLarge']    = function() { return $this->getThumbData(MediaCore::resizeMaxSide, 1400); };
        } elseif ($this->type == self::typeStoryImage) {
            $fields['thumb']         = function() { return $this->getThumbData(MediaCore::resizeSquareCrop, $this->getOption('mainThumbDimension')); };
            $fields['thumbLarge']    = function() { return $this->getThumbData(MediaCore::resizeSquareCrop, $this->getOption('largeThumbDimension')); };
        } else {
            $fields['thumb']         = function() { return $this->getThumbData(MediaCore::resizeMaxSide, $this->getOption('mainThumbDimension')); };
            $fields['thumbLarge']    = function() { return $this->getThumbData(MediaCore::resizeMaxSide, $this->getOption('largeThumbDimension')); };
        }

        if ($this->scenario == 'feed') {
            $fields['story']         = function() { return $this->targetStory; };
        }

        if ($this->target_type == Story::typeId) {
            $fields['date'] = 'date';
            $fields['timestamp'] = 'time_created';
        }

        return $fields;
    }

    /**
    *   Get connecting condition
    **/
    public function getBrotherCondition($type = 'default') {
        if (empty($this->target_id) || empty($this->target_type)) return null;

        $condition = null;

        if ($type == 'default')
            $condition = ['target_id' => $this->target_id, 'target_type' => $this->target_type, 'type' => $this->type, 'is_deleted' => 0];
        elseif ($type == 'target')
            $condition = ['target_id' => $this->target_id, 'target_type' => $this->target_type, 'is_deleted' => 0];

        if ($this->target_type == Story::typeId) $condition['date'] = $this->date;

        return $condition;
    }

    /**
    *   Active items condition
    **/
    public static function getActiveCondition() {
        return ['is_deleted' => 0];
    }

    /**
    *   After the item was uploaded
    **/
    public function afterUpload() {
        // Mark Predecessors as deleted
        if ($this->type == self::typeStoryImage) {
            $target = $this->targetStory;

            if ($target) {
                $target->media_count ++;
                $target->save();
            }

            $oldies = $this->find()->where(self::makeCondition(['target_id' => $this->target_id, 'target_type' => $this->target_type, 'is_deleted' => 0, 'date' => $this->date, 'id' => ['!=', $this->id]]))->all();

            foreach ($oldies as $pred) {
                $pred->markDeleted();
            }
        }
    }

    /**
    *   After the media item was deleted
    **/
    public function afterDelete() {
        if ($this->type == self::typeStoryImage) {
            $target = $this->targetStory;
            if ($target) $target->media_count --;
            $target->save();
        }
    }

    /**
     * Target relation
     */
    public function getTargetStory() {
        return $this->hasOne(Story::className(), ['id' => 'target_id']);
    }
}
