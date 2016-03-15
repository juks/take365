<?php

use app\models\Story;
use app\assets\StoryAsset;
use app\components\Helpers;

StoryAsset::register($this);
$lastMonth = null;

$this->registerJs("initStory();");

?>
<div id="userPhotos" class="user-photos">
  <?php if ($story): ?>
  <?php if ($canManage): ?>
  <form id="storyEditForm" class="editable-not-editing form" action="/api/story/write" method="post" name="storyEditForm">
    <input name="id" type="hidden" value="<?= $story->id ?>">
    <h1 class="story-title editable"><span class="editable-placeholder<?php if ($story->title): ?> hidden<?php endif ?>">Кликните, чтобы добавить заголовок</span><span class="editable-text"><?= $story->title ?></span><input class="editable-input" placeholder="Кликните, чтобы добавить заголовок" type="text" maxlength="255" value="<?= $story->title ?>" name="title"></h1>
    <p class="story-desc editable"><span class="editable-placeholder<?php if ($story->description): ?> hidden<?php endif ?>">Кликните, чтобы добавить описание</span><span class="editable-text"><?= $story->description ?></span><textarea class="editable-input" placeholder="Кликните, чтобы добавить описание" name="description"><?= $story->description ?></textarea></p>
    <p class="story-submit"><input value="Сохранить" type="submit" class="editable-submit"> <input value="Отмена" type="reset" class="editable-cancel"></p>
    <table id="settingsHolder">
    <tr id="statusSelectorHoder"<?php if ($story->is_deleted): ?> class="hidden"<?php endif ?>>
    <td>Статус</td>
    <td><select name="status" id="storyStatusSelector" onchange="Story.updateStatus(pp.storyId); return false;">
      <option value="<?= Story::statusHidden ?>"<?php if ($story->status == Story::statusHidden): ?> selected<?php endif ?>>Скрыта</option>
      <option value="<?= Story::statusPublic ?>"<?php if ($story->status == Story::statusPublic): ?> selected<?php endif ?>>В публичном доступе</option>
    </select></td>
    </tr>
    </table>
    <div class="story-info">
      <a href="#" class="num-comments"><span class="fa fa-comment-o"></span> 13 комментариев</a>
      <a href="#" id="delete-recover" onclick="Story.deleteRecover(<?= $story->id ?>); return false;"><?php if ($story->is_deleted): ?><span class="recover">Восстановить историю</span><?php else: ?><span class="delete">Удалить историю</span><?php endif ?></a>
    </div>
  </form>
  <?php else: ?>
  <h1 class="story-title1"><?= $story->titleFilled ?></h1>
  <p class="story-desc"><?= $story->description_jvx ?></p>
  <div class="story-info">
    <p>Автор истории —  <a href="<?= $user->url ?>"><?= $user->fullnameFilled ?></a></p>
    <p><a href="#" class="num-comments"><span class="fa fa-comment-o"></span> 13 комментариев</a></p>
  </div>
  <?php endif ?>
  <div id="socialBlock" class="element">
  <?= $this->render('/blocks/socialBlock', ['fbAppId' => Helpers::getParam('components/authClientCollection/facebook/clientId'), 'pageUrl' => (\yii\helpers\Url::base(true) . \Yii::$app->request->url)]) ?>
  </div>
  <div class="cl"></div>
  <div class="yearTitle"><?= $story->yearStart ?>—<?= $story->yearEnd ?></div>
  <?php foreach ($story->calendar as $day): ?>
  <?php if ($lastMonth != $day['monthTitle']): ?><p class="hugeTitle tbo story-mounth cl"><?= $day['monthTitle'] ?></p><?php endif ?>
    <div <?php if (!empty($day['id'])): ?> data-id="<?= $day['id'] ?>" <?php endif ?>id="day-<?= $day['date'] ?>" class="user-photo available<?php if (!empty($day['isEmpty'])): ?> empty i-upload<?php endif ?>">
      <div class="user-photo-day"><?= $day['monthDay'] ?></div>
      <?php if (empty($day['isEmpty'])): ?>
        <?php if (empty($day['invisible'])): ?>
        <div class="user-photo-content">
          <a<?php /* href="<?= $day['url'] ?>"*/ ?>><img src="<?= $day['image']['url'] ?>" width="<?= $day['image']['width'] ?>" height="<?= $day['image']['height'] ?>" class="user-photo-image"></a>
          <?php if ($canUpload): ?><div class="user-photo-manage">Редактировать</div><?php endif ?>
          <?php if (!empty($day['isDeleted'])): ?><div class="user-photo-restore"><a class="ctrl-restore" onclick="Story.recoverMedia('<?= $day['date'] ?>')">Восстановить</a> или <a class="ctrl-replace i-upload" onclick="Story.openUpload('<?= $day['date'] ?>')">заменить</a>.</div><?php endif ?>
        </div>
        <?php endif ?>
      <?php endif ?>
    </div>
  <?php
          $lastMonth = $day['monthTitle'];
  ?>
  <?php endforeach ?>
  <?php endif ?>
</div>
<div class="comments">
  <h2 class="comments-title">Комментарии (<?= $story->comments_count ?>)</h2>
  <?php if (count($story->comments)): ?>
  <?php foreach ($story->comments as $comment): ?>
  <?php require(__DIR__ . '/../blocks/comment.php'); ?>
  <?php endforeach ?>
  <?php endif ?>
  <div>А дальше — Серёжа нахардкодил. Паша, выпили это, пожалуйста</div>
  <div class="comments-reply">
    <div class="comment">
      <div class="comment-header">
        <div class="comment-user fa fa-user">
          <a href="#" class="comment-user-img"></a>
        </div>
        <div class="comment-username"><a href="#">Lusever</a></div>
        <time class="comment-date">6 марта 2016, 16:13</time>
        <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
      </div>
      <div class="comment-text">
        А мне Diamond Eyes дико зашёл после концерта, в живую треки из него прям вписались в общее представление о группе.
      </div>
      <div class="comment-options">
        <a href="#" class="comment-options-item">Ответить</a>
      </div>
    </div>
    <div class="comments-reply">
      <div class="comment comment-my">
        <div class="comment-header">
          <div class="comment-user fa fa-user">
            <a href="#" class="comment-user-img" style="background-image: url(http://dev.take365.org/media/p1/userpic/8e/611/me.jpg);"></a>
          </div>
          <div class="comment-username"><a href="#">Бей лбом</a></div>
          <time class="comment-date">6 марта 2016, 17:11</time>
          <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
          <div class="comment-trash"><a href="#" title="Удалить комментарий" class="fa fa-trash-o"></a></div>
        </div>
        <div class="comment-text">
          Doomed User уже на концертах играют, походу будет следующим синглом.
        </div>
        <div class="comment-options" style="display: none;">
          <a href="#" class="comment-options-item">Ответить</a>
        </div>
        <form action="/" class="form form-comment">
          <fieldset>
            <textarea name="" id="" cols="30" rows="10"></textarea>
          </fieldset>
          <fieldset>
            <input type="submit" value="Отправить">
            <a href="#" class="cancel">Отмена</a>
          </fieldset>
        </form>
      </div>
      <div class="comments-reply">
        <div class="comment">
          <div class="comment-header">
            <div class="comment-user fa fa-user">
              <a href="#" class="comment-user-img"></a>
            </div>
            <div class="comment-username"><a href="#">Lusever</a></div>
            <time class="comment-date">6 марта 2016, 17:47</time>
            <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
          </div>
          <div class="comment-text">
            Что то очень интересное по моему... Такое неоднозначное, но притягательное...))
          </div>
          <div class="comment-options">
            <a href="#" class="comment-options-item">Ответить</a>
          </div>
        </div>
        <div class="comments-reply">
          <div class="comment">
            <div class="comment-header">
              <div class="comment-user fa fa-user">
                <a href="#" class="comment-user-img"></a>
              </div>
              <div class="comment-username"><a href="#">Oracle</a></div>
              <time class="comment-date">6 марта 2016, 17:51</time>
              <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
            </div>
            <div class="comment-text">
              А у них всегда так, помню когда роузмери лайв только появился как все умилялись, а потом альбомная версия уже не торт.
            </div>
            <div class="comment-options">
              <a href="#" class="comment-options-item">Ответить</a>
            </div>
          </div>
          <div class="comments-reply">
            <div class="comment">
              <div class="comment-header">
                <div class="comment-user fa fa-user">
                  <a href="#" class="comment-user-img"></a>
                </div>
                <div class="comment-username"><a href="#">Juks</a></div>
                <time class="comment-date">6 марта 2016, 18:24</time>
                <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
              </div>
              <div class="comment-text">
                Слушай, а ведь реально так!
              </div>
              <div class="comment-options" style="display: none;">
                <a href="#" class="comment-options-item">Ответить</a>
              </div>
              <form action="/" class="form form-comment">
                <fieldset class="error">
                  <textarea name="" id="" cols="30" rows="10"></textarea>
                  <span class="error-message">Зачем отправлять пустое сообщение, бро?</span>
                </fieldset>
                <fieldset>
                  <input type="submit" value="Отправить">
                  <a href="#" class="cancel">Отмена</a>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="comment">
      <div class="comment-header">
        <div class="comment-user fa fa-user">
          <a href="#" class="comment-user-img"></a>
        </div>
        <div class="comment-username"><a href="#">Juks</a></div>
        <time class="comment-date">6 марта 2016, 16:52</time>
        <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
      </div>
      <div class="comment-text">
        Ха, самым слабым считаю Diamond Eyes, ни одна песня не понравилась, второй плохиш Saturday Night Wrist. Самый крутой 1997 года, второй WP, третий Deftones. KNY - уже звучит как другая группа но понравился.
      </div>
      <div class="comment-options">
        <a href="#" class="comment-options-item">Ответить</a>
      </div>
    </div>
    <div class="comment">
      <div class="comment-header">
        <div class="comment-user fa fa-user">
          <a href="#" class="comment-user-img" style="background-image: url(http://dev.take365.org/media/p1/userpic/8e/611/me.jpg);"></a>
        </div>
        <div class="comment-username"><a href="#">Бей лбом</a></div>
        <time class="comment-date">6 марта 2016, 16:52</time>
        <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
      </div>
      <div class="comment-text">
        Вкатил круче первого сингла, даже с таким звуком)
      </div>
      <div class="comment-options">
        <a href="#" class="comment-options-item">Ответить</a>
      </div>
    </div>
  </div>
  <div class="comment">
    <div class="comment-header">
      <div class="comment-user fa fa-user">
        <a href="#" class="comment-user-img" style="background-image: url(http://dev.take365.org/media/p1/userpic/8e/611/me.jpg);"></a>
      </div>
      <div class="comment-username"><a href="#">Бей лбом</a></div>
      <time class="comment-date">6 марта 2016, 18:02</time>
      <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
    </div>
    <div class="comment-text">
      Говнозвук конечно, наверное любимый трек Стефа с нового, дали оторваться.
    </div>
    <div class="comment-options">
      <a href="#" class="comment-options-item">Ответить</a>
    </div>
  </div>
  <div class="comment">
    <div class="comment-header">
      <div class="comment-user fa fa-user">
        <a href="#" class="comment-user-img"></a>
      </div>
      <div class="comment-username"><a href="#">Lusever</a></div>
      <time class="comment-date">7 марта 2016, 02:17</time>
      <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
    </div>
    <div class="comment-text">
      Они во время вайт пони и одноимённика вообще чуть не поубивали друг друга.
    </div>
    <div class="comment-options">
      <a href="#" class="comment-options-item">Ответить</a>
    </div>
  </div>
  <div class="comment">
    <div class="comment-header">
      <div class="comment-user fa fa-user">
        <a href="#" class="comment-user-img"></a>
      </div>
      <div class="comment-username"><a href="#">Oracle</a></div>
      <time class="comment-date">7 марта 2016, 03:41</time>
      <div class="comment-url"><a href="#" title="Ссылка на комментарий">#</a></div>
    </div>
    <div class="comment-text">
      А мне новая песнь больше всего ликнутый Чино Smile по настроению напомнила.
    </div>
    <div class="comment-options">
      <a href="#" class="comment-options-item">Ответить</a>
    </div>
  </div>
  <div class="comments-footer">
    <form action="/" class="form form-comment">
      <fieldset>
        <legend>Ваш комментарий</legend>
      </fieldset>
      <fieldset>
        <textarea name="" id="" cols="30" rows="10"></textarea>
      </fieldset>
      <fieldset>
        <input type="submit" value="Отправить">
        <a href="#" class="cancel">Отмена</a>
      </fieldset>
    </form>
    <div class="comments-info">
      <span class="fa fa-exclamation-triangle"></span>
      Только зарегистрированные пользователи могут участвовать в обсуждениях.<br>
      Если вы хотите оставить комментарий, пожалуйста, <a href="#">представьтесь</a> или <a href="#">зарегистрируйтесь</a>.
    </div>
  </div>
</div>
