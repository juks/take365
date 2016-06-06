<?php

use app\models\Story;
use app\models\Media;
use app\assets\StoryAsset;
use app\components\Helpers;
use app\components\Ml;

StoryAsset::register($this);
$dimension = Media::getMediaOptions(Media::aliasStoryImage)[Media::largeThumbDimension];

$this->registerJs("feedRender(document.getElementById('feed'),{userId:$user->id 0});");

?>

<div id="feed"></div>
