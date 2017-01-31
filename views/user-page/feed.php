<?php

use app\models\Story;
use app\models\Media;
use app\assets\StoryAsset;
use app\components\Helpers;
use app\components\Ml;

StoryAsset::register($this);
$dimension = Media::getMediaOptions(Media::aliasStoryImage)[Media::largeThumbDimension];

if ($isSubscribed) {
  $reactUser =  \yii\helpers\Json::encode($owner);
  $this->registerJs("feedRender(document.getElementById('feed'),{user:$reactUser});");
}
?>

<?php if (!$isSubscribed): ?>
  <section class="content feed">
    <p>Пользователь не подписан на обновления!</p>
  </section>
<?php endif ?>
<section id="feed" class="content feed"></section>
