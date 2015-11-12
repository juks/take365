<?php

namespace app\models\mediaExtra;

use Yii;
use app\models\Media;

trait TMediaUploadExtra {
    /**
     * Uploads media and attaches it to this object
     * @param mixed $mediaResource media resource to upload
     * @param string $mediaType media type name
     */
	public function addMedia($mediaResource, $mediaType) {
		$media = new Media();
		return $media->takeFile($mediaResource, $mediaType, $this);
	}
}