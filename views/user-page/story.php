<?php

use app\models\Story;
use app\assets\StoryAsset;
use app\components\Helpers;
use app\components\Ml;

StoryAsset::register($this);
$lastMonth = null;

$reactComments =  \yii\helpers\Json::encode($story->comments);
$reactUser =  \yii\helpers\Json::encode($user);

$this->registerJs("initStory();appRender(document.getElementById('comments'),{comments:$reactComments,id:$story->id,user:$reactUser})");

?>

<main class="content stories" id="userPhotos">
  <header class="content-header">
    <?php if ($story): ?>
    <?php if ($canManage): ?>
      <form id="storyEditForm" class="editable-not-editing form" action="/api/story/write" method="post" name="storyEditForm">
        <input name="id" type="hidden" value="<?= $story->id ?>">
        <h1 class="story-title editable">
          <span class="editable-placeholder<?php if ($story->title): ?> hidden<?php endif ?>">Кликните, чтобы добавить заголовок</span>
          <span class="editable-text"><?= $story->title ?></span>
          <input class="editable-input" placeholder="Кликните, чтобы добавить заголовок" type="text" maxlength="255" value="<?= $story->title ?>" name="title">
        </h1>
        <div class="story-desc editable">
          <span class="editable-placeholder<?php if ($story->description): ?> hidden<?php endif ?>">Кликните, чтобы добавить описание</span>
          <span class="editable-text"><?= $story->description ?></span>
          <textarea class="editable-input" placeholder="Кликните, чтобы добавить описание" name="description"><?= $story->description ?></textarea>
        </div>
        <div class="story-submit">
          <input value="Сохранить" type="submit" class="editable-submit">
          <input value="Отмена" type="reset" class="editable-cancel">
        </div>
        <div class="story-settings<?php if ($story->is_deleted): ?> hidden<?php endif ?>" id="statusSelectorHoder">
          <label>Статус:</label>
          <select name="status" id="storyStatusSelector" onchange="Story.updateStatus(pp.storyId); return false;">
            <option value="<?= Story::statusHidden ?>"<?php if ($story->status == Story::statusHidden): ?> selected<?php endif ?>>Скрыта</option>
            <option value="<?= Story::statusPublic ?>"<?php if ($story->status == Story::statusPublic): ?> selected<?php endif ?>>В публичном доступе</option>
          </select>
        </div>
        <div class="story-footer">
          <a href="#comments" class="num-comments"><?= Ml::t('{n,plural,=0{No comments} =1{One Comment} other{# Comments}}', null, ['n' => $story->comments_count ]) ?></a>
          <span class="sep">·</span>
          <a href="#" class="edit" onClick="$('#storyEditForm').toggleClass('editable-editing editable-not-editing')">Редактировать историю</a>
          <span class="sep">·</span>
          <a href="#" class="delete-recover" id="delete-recover" onclick="Story.deleteRecover(<?= $story->id ?>); return false;"><?php if ($story->is_deleted): ?><span class="recover">Восстановить историю</span><?php else: ?><span class="delete">Удалить историю</span><?php endif ?></a>
        </div>
      </form>

    <?php else: ?>
      <h1 class="story-title"><?= $story->titleFilled ?></h1>
      <div class="story-desc">
        <p>Автор истории —  <a href="<?= $owner->url ?>"><?= $owner->fullnameFilled ?></a></p>
        <?php if ($story->description_jvx): ?>
          <p><?= $story->description_jvx ?></p>
        <?php endif ?>
      </div>
      <div class="story-footer">
        <a href="#comments" class="num-comments"><?= Ml::t('{n,plural,=0{No comments} =1{One Comment} other{# Comments}}', null, ['n' => $story->comments_count ]) ?></a>
      </div>

    <?php endif ?>
      <div class="social">
        <?= $this->render('/blocks/socialBlock', ['fbAppId' => Helpers::getParam('components/authClientCollection/facebook/clientId'), 'pageUrl' => (\yii\helpers\Url::base(true) . \Yii::$app->request->url)]) ?>
      </div>

    <?php endif ?>
  </header>

  <?php if ($story): ?>
    <h2><?= $story->yearStart ?>—<?= $story->yearEnd ?></h2>

    <?php foreach ($story->calendar as $day): ?>
      <?php if ($lastMonth != $day['monthTitle']): ?>
        <?php if ($lastMonth): ?>
            </ul>
          </section>
        <?php endif ?>
        <section class="story-month">
          <h3><?= $day['monthTitle'] ?></h3>
            <ul class="story-list">
      <?php endif ?>

          <li <?php if (!empty($day['id'])): ?> data-id="<?= $day['id'] ?>" <?php endif ?>id="day-<?= $day['date'] ?>" class="story-item <?= !empty($day['isUploadable']) ? 'available' : 'not-available' ?><?php if (!empty($day['isEmpty'])): ?> story-item-empty<?php if ($canManage): ?> upload<?php endif ?><?php endif ?>">
            <div class="story-day"><?= $day['monthDay'] ?></div>
            <div class="story-content fa fa-file-image-o">

            <?php if (empty($day['isEmpty'])): ?>
              <?php if (empty($day['invisible'])): ?>
                <a href="<?= $day['url'] ?>" class="story-img-wrap"><img src="<?= $day['image']['url'] ?>" width="<?= $day['image']['width'] ?>" height="<?= $day['image']['height'] ?>" class="story-img"></a>

                <?php if ($canUpload): ?><div class="story-edit">Редактировать</div><?php endif ?>
                <?php if (!empty($day['isDeleted'])): ?><div class="user-photo-restore"><a class="ctrl-restore" onclick="Story.recoverMedia('<?= $day['date'] ?>')">Восстановить</a> или <a class="ctrl-replace i-upload" onclick="Story.openUpload('<?= $day['date'] ?>')">заменить</a>.</div><?php endif ?>
                  <?php if ($user): ?>
                    <div class="story-img-likes">
                      <a href="#" class="fa fa-heart<?= $day['isLiked'] ? '' : '-o' ?> story-img-like"></a>
                      <span class="story-img-like-total"><?= $day['likesCount'] ?: '' ?></span>
                    </div>
                  <?php elseif (!empty($day['likesCount'])): ?>
                    <div class="story-img-likes">
                      <span class="fa fa-heart story-img-like"></span>
                      <span class="story-img-like-total"><?= $day['likesCount'] ?: '' ?></span>
                    </div>
                  <?php endif ?>
              <?php endif ?>
            <?php endif ?>
            </div>
          </li>

      <?php
        $lastMonth = $day['monthTitle'];
      ?>
    <?php endforeach ?>
      </ul>
    </section>
  <?php endif ?>

  <div class="comments" id="comments"></div>
</main>
