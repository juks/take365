<?php

namespace app\models\mediaExtra;

use Yii;
use app\models\Media;

trait TMediaUploadExtra {
    /**
     * Uploads media and attaches it to this object
     * @param mixed $mediaResource media resource to upload
     * @param string $mediaType media type name
     * @param string $instance
     * @param array $extra
     */
	public function addMedia($mediaResource, $mediaType, $instance, $extra = []) {
        if (!$instance) $instance = new Media();
        $extra[Media::thumbsCreate] = true;

		$instance->takeFile($mediaResource, $mediaType, $this, $extra);

		return $instance;
	}
}