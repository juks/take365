<?php

use app\assets\StoryAsset;
use app\components\Helpers;
use app\components\Ml;

StoryAsset::register($this);

$subscribedCount = $owner->subscribedCount;
$subscribersCount = $owner->subscribersCount;

$this->registerJs("initStoriesIndex();");
if (!Yii::$app->user->isGuest && !$owner->thisIsMe) {
  $this->registerJs("followRender(document.getElementById('follow'),{storyUserId:$owner->id,isFollowing:" . json_encode($isFollowing) ."});");
}

?>

<main class="content stories">
  <header class="content-header">
    <div class="stories-user">
      <div class="fa fa-user">
        <a class="stories-user-img" href="<?= $owner->urlProfile ?>" <?php if ($owner->userpic): ?> style="background-image: url(<?= $owner->userpic['t']['maxSide']['500']['url']; ?>)"<?php endif ?>></a>
      </div>
      <div class="stories-desc">
        <h1 class="stories-title"><?php if ($owner->thisIsMe): ?><?= $owner->fullnameFilled ?><?php else: ?><?= $owner->fullnameFilled ?> <span id="follow"></span><?php endif ?></h1>
        <?php if ($subscribedCount || $subscribersCount): ?>
          <p class="stories-subscribers">
            <?php if($subscribedCount): ?>Он читает: <a href="<?= $owner->urlProfile ?>#subscribers"><?= $subscribedCount ?></a><?= $subscribersCount ? ', ' : '' ?><?php endif ?>
            <?php if($subscribersCount): ?><?= $subscribedCount ? 'е' : 'E' ?>го чита<?= $subscribersCount > 1 ? 'ют' : 'ет' ?>: <a href="<?= $owner->urlProfile ?>#subscribers"><?= $subscribersCount ?></a><?php endif ?>
          </p>
        <?php endif ?>
        <?php if ($owner->thisIsMe && $owner->isPoorProfile): ?>
          <p class="stories-profile"><a href="<?= $owner->urlEdit ?>">Заполнить профиль</a></p>
        <?php endif ?>
      </div>
    </div>
  </header>

  <?php if ($stories): ?>
  <?php foreach ($stories as $story): ?>
  <section class="story<?php if ($story->isDeleted): ?> story-deleted<?php elseif ($story->isHidden): ?> story-hidden<?php elseif ($story->progress['isComplete']): ?> story-success<?php endif ?>">
    <header class="story-header">
      <h2 class="story-title"><a href="<?= $story->url ?>"><?= $story->titleFilled ?></a></h2>
    </header>
    <?php include('mediaBlock.php') ?>
    <?php if ($story->progress): ?><div class="story-summary">Загружено <?= $story->progress['totalImages'] ?> <?= $story->progress['totalImagesTitle'] ?> из <?= $story->progress['totalDays'] ?><?php if ($story->progress['percentsComplete'] > 5): ?> (<?= $story->progress['percentsComplete'] ?>%)<?php endif ?> <?php if ($story->progress['delayDaysMakeSense']): ?> <span class="story-summary-info"><?= $story->progress['delayDays'] ?> <?= $story->progress['delayDaysTitle'] ?> отставания</span><?php endif ?><span class="story-summary-info"><a href="<?= $story->urlComments ?>" class="num-comments"><?= Ml::t('{n,plural,=0{No comments} =1{One Comment} other{# Comments}}', null, ['n' => $story->comments_count ]) ?></a></span></div>
    <div class="story-status">
       <!--<span class="fa fa-circle-o-notch fa-spin"></span>-->
        <span class="story-status-value"><?php if ($story->progress['isComplete']): ?>Полностью завершена<?php elseif ($story->isDeleted): ?>Удалена<?php elseif ($story->isHidden): ?>Скрыта<?php endif ?></span>
       <!--<span class="story-status-sep">·</span><a href="#">Удалить историю</a>-->
    </div>
    <?php endif ?>
  </section>
  <?php endforeach ?>

  <?php else: ?>
  <section class="story">
    <p>
    <?php if ($owner->thisIsMe): ?>
    У вас нет доступных историй!
    <?php else: ?>
    У этого человека нет доступных историй.
    <?php endif ?>
    </p>
  </section>
  <?php endif ?>

  <?php if ($canCreate): ?>
  <p>
    Вы можете начать новую историю, просто <a href="#" id="startNewStory2" class="start-new-story">загрузив фотографию</a>.<br>
    Фотографию следующего дня можно будет загрузить завтра, послезавтра — ещё, и так далее.
  </p>
  <?php endif ?>
</main>
