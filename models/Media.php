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
    const typeStoryImage    = 2;

    protected static $_globalOptions = [
                                    Media::typeUserpic => [
                                                        MediaCore::typeId                => 1,
                                                        MediaCore::targetType            => 1,
                                                        MediaCore::alias                 => 'userpic',
                                                        MediaCore::cleanPrev             => true,
                                                        MediaCore::allowedFormats        => [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG],
                                                        MediaCore::maxFileSize           => 1048576 * 10,
                                                        MediaCore::resizeMode            => MediaCore::resizeMaxSide,
                                                        MediaCore::targetDimension       => 3000,
                                                        MediaCore::thumbsList            => [
                                                                                            MediaCore::resizeMaxSide => [200, 100, 50],
                                                                                        ],
                                                        MediaCore::mainThumbDimension    => 100,
                                                        MediaCore::largeThumbDimension   => 200,
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
                                                        MediaCore::alias                 => 'storyImage',
                                                        MediaCore::cleanPrev             => true,
                                                        MediaCore::allowedFormats        => [IMAGETYPE_JPEG, IMAGETYPE_PNG],
                                                        MediaCore::maxFileSize           => 1048576 * 10,
                                                        MediaCore::resizeMode            => MediaCore::resizeMaxSide,
                                                        MediaCore::targetDimension       => 3000,
                                                        MediaCore::thumbsList            => [
                                                                                            MediaCore::resizeMaxSide => [400, 200, 100, 50],
                                                                                        ],
                                                        MediaCore::mainThumbDimension    => 200,
                                                        MediaCore::largeThumbDimension   => 400,
                                                        MediaCore::quality               => 95,
                                                        MediaCore::engine                => MediaCore::engineImageMagick,
                                                        MediaCore::resizeFilter          => \Imagick::FILTER_BLACKMAN,
                                                        MediaCore::resizeBlur            => 0.86,
                                                        MediaCore::thumbQuality          => 96,
                                                        MediaCore::saveExif              => true,
                                                        MediaCore::autoOrient            => true
                                                    ]
                                ];

    public function getBrotherCondition($type = 'default') {
        if (empty($this->target_id) || empty($this->target_type))
            return null;

        $condition = null;

        if ($type == 'default')
            $condition = ['target_id' => $this->target_id, 'target_type' => $this->target_type, 'type' => $this->type, 'is_deleted' => 0];
        elseif ($type == 'target')
            $condition = ['target_id' => $this->target_id, 'target_type' => $this->target_type, 'is_deleted' => 0];

        if ($this->target_type == Story::typeId) $condition['date'] = $this->date;

        return $condition;
    }
}