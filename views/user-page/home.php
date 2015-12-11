<header class="article-header">
  <h1 class="article-title"><?php if ($user->thisIsMe): ?>Привет, <a href="<?= $user->urlProfile ?>"><?= $user->username ?></a>!<?php else: ?>Истории <a href="<?= $user->urlProfile ?>"><?= $user->username ?></a><?php endif ?></h1>
</header>
<?php if ($stories): ?>
<?php foreach ($stories as $story): ?>
<section class="story<?php if ($story->is_deleted): ?> story-deleted<?php elseif ($story->progress['isComplete']): ?> story-success<?php endif ?>">
  <div class="story-content">
    <h2><a href="<?= $story->url ?>"><?= $story->titleFilled ?></a></h2>
    <?php include('mediaBlock.php') ?>
    <?php if ($story->progress): ?><p class="story-status-upload">Загружено <?= $story->progress['totalImages'] ?> <?= $story->progress['totalImagesTitle'] ?> из <?= $story->progress['totalDays'] ?> (<?= $story->progress['percentsComplete'] ?>%) <?php if ($story->progress['delayDaysMakeSense']): ?> <span class="story-status-lag"><?= $story->progress['delayDays'] ?> <?= $story->progress['delayDaysTitle'] ?> отставания</span><?php endif ?></p>
    <p class="story-status">
       <!--<span class="fa fa-circle-o-notch fa-spin"></span>-->
        <span class="story-status-value">{{ IF $isComplete }}Полностью завершена{{ ELSEIF $isDeleted }}Удалена{{ ELSEIF $isHidden }}Скрыта{{ END }}</span>
       <!--<span class="story-status-sep">·</span><a href="#">Удалить историю</a>-->
    </p>
    <?php endif ?>
  </div>
</section>
<?php endforeach ?>

<?php else: ?>
<section class="story">
  <div class="story-content">
    <p>
    {{ IF thisIsMe }}
    У вас нет доступных историй!
    {{ ELSE }}
    У этого человека нет доступных историй.
    {{ END }}
    </p>
  </div>
</section>
<?php endif ?>

<?php if ($canCreate): ?>
<p>
  Вы можете начать новую историю, просто <a href="#" id="startNewStory">загрузив фотографию</a>.<br>
  Фотографию следующего дня можно будет загрузить завтра, послезавтра — ещё, и так далее.
</p>
<?php endif ?>