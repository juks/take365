<?php

use app\models\Story;
use app\models\Media;
use app\assets\StoryAsset;
use app\components\Helpers;
use app\components\Ml;

StoryAsset::register($this);
$dimension = Media::getMediaOptions(Media::aliasStoryImage)[Media::largeThumbDimension];


?>

<?php foreach($feed as $item): ?>
<div style="padding: 0 0 25px 0">
  <div>
    <img src="<?= $item['t'][Media::resizeSquareCrop][$dimension]['url'] ?>" width="<? $item['t'][Media::resizeSquareCrop][$dimension]['width'] ?>" height="<?= $item['t'][Media::resizeSquareCrop][$dimension]['height'] ?>">
  </div>
  <div>
    <a href="<?= $item->creator->url ?>"><?= $item->creator->usernameFilled ?></a> &rarr; <a href="<?= $item->targetStory->url ?>"><?= $item->targetStory->titleFilled ?></a>
  </div>
</div>
<?php endforeach; ?>
