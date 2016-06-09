<?php

use app\assets\StoryAsset;
use app\components\Helpers;
use app\components\Ml;

StoryAsset::register($this);

$this->registerJs("initStoriesIndex();");
if (!Yii::$app->user->isGuest && !$owner->thisIsMe) {
  $this->registerJs("followRender(document.getElementById('follow'),{storyUserId:$owner->id,isFollowing:" . json_encode($isFollowing) ."});");
}

?>

<header class="article-header">
  <h1 class="article-title"><?php if ($owner->thisIsMe): ?>Привет, <a href="<?= $owner->urlProfile ?>"><?= $owner->fullnameFilled ?></a>!<?php else: ?>Истории <a href="<?= $owner->urlProfile ?>"><?= $owner->fullnameFilled ?></a> <span id="follow"></span><?php endif ?></h1>
  <?php if ($canCreate): ?>
    <!--<span class="fa fa-plus-square-o start-new-story" id="startNewStory1" title="Создать новую историю"></span>-->
  <?php endif ?>
</header>
<?php if ($stories): ?>
<?php foreach ($stories as $story): ?>
<section class="story<?php if ($story->isDeleted): ?> story-deleted<?php elseif ($story->progress['isComplete']): ?> story-success<?php endif ?>">
  <div class="story-content">
    <h2><a href="<?= $story->url ?>"><?= $story->titleFilled ?></a></h2>
    <?php include('mediaBlock.php') ?>
    <?php if ($story->progress): ?><div class="story-summary">Загружено <?= $story->progress['totalImages'] ?> <?= $story->progress['totalImagesTitle'] ?> из <?= $story->progress['totalDays'] ?> (<?= $story->progress['percentsComplete'] ?>%) <?php if ($story->progress['delayDaysMakeSense']): ?> <span class="story-summary-info"><?= $story->progress['delayDays'] ?> <?= $story->progress['delayDaysTitle'] ?> отставания</span><?php endif ?><span class="story-summary-info"><a href="<?= $story->urlComments ?>"><span class="fa fa-comment-o"></span> <?= Ml::t('{n,plural,=0{No comments} =1{One Comment} other{# Comments}}', null, ['n' => $story->comments_count ]) ?></a></span></div>
    <div class="story-status">
       <!--<span class="fa fa-circle-o-notch fa-spin"></span>-->
        <span class="story-status-value"><?php if ($story->progress['isComplete']): ?>Полностью завершена<?php elseif ($story->isDeleted): ?>Удалена<?php elseif ($story->isHidden): ?>Скрыта<?php endif ?></span>
       <!--<span class="story-status-sep">·</span><a href="#">Удалить историю</a>-->
    </div>
    <?php endif ?>
  </div>
</section>
<?php endforeach ?>

<?php else: ?>
<section class="story">
  <div class="story-content">
    <p>
    <?php if ($owner->thisIsMe): ?>
    У вас нет доступных историй!
    <?php else: ?>
    У этого человека нет доступных историй.
    <?php endif ?>
    </p>
  </div>
</section>
<?php endif ?>

<?php if ($canCreate): ?>
<p>
  Вы можете начать новую историю, просто <a href="#" id="startNewStory2" class="start-new-story">загрузив фотографию</a>.<br>
  Фотографию следующего дня можно будет загрузить завтра, послезавтра — ещё, и так далее.
</p>
<?php endif ?>
