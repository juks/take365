<?php

use app\models\Story;
use app\models\Media;
use app\assets\StoryAsset;
use app\components\Helpers;
use app\components\Ml;

StoryAsset::register($this);
$dimension = Media::getMediaOptions(Media::aliasStoryImage)[Media::largeThumbDimension];

if ($isSubscribed) $this->registerJs("feedRender(document.getElementById('feed'),{userId:$owner->id});");

?>

<?php if (!$isSubscribed): ?><div>Пользователь не подписан на обновления!</div><?php endif ?>
<div id="feed"></div>
