<?php

use app\assets\StoryAsset;
use app\components\Helpers;

StoryAsset::register($this);

$this->registerJs("initStory();
$('.notifyTip').tooltipsy({
	offset: [0, 1],
	delay: 200
})");
?>
<?php
	//var_dump($story);die()
?>
<?php if ($story): ?>
<?php if ($canManage): ?>
<form id="storyEditForm" class="editable-not-editing form" action="/story/write/" method="post" name="storyEditForm">
	<input name="storyId" type="hidden" value="<?= $story->id ?>">
	<h1 class="story-title editable"><span class="editable-placeholder<?php if ($story->title): ?> hidden<?php endif ?>">Кликните, чтобы добавить заголовок</span><span class="editable-text"><?= $story->title ?></span><input class="editable-input" placeholder="Кликните, чтобы добавить заголовок" type="text" maxlength="255" value="<?= $story->title ?>" name="title"></h1>
	<p class="story-desc editable"><span class="editable-placeholder<?php if ($story->description): ?> hidden<?php endif ?>">Кликните, чтобы добавить описание</span><span class="editable-text"><?= $story->description ?></span><textarea class="editable-input" placeholder="Кликните, чтобы добавить описание" name="description"><?= $story->description ?></textarea></p>
	<p class="story-submit"><input value="Сохранить" type="submit" class="editable-submit"> <input value="Отмена" type="reset" class="editable-cancel"></p>
	<table id="settingsHolder">
	<tr>
	<td>Статус</td>
	<td><select name="status" id="storyStatusSelector" onchange="Story.updateStatus(pp.storyId); return false;">
		<option value="0"<?php if ($story->status == 0): ?> selected<?php endif ?>>Скрыта</option>
		<option value="1"<?php if ($story->status == 1): ?> selected<?php endif ?>>В публичном доступе</option>
	</select></td>
	</tr>
	</table>
	<p><a href="#" id="delete-recover" class="small" onclick="Story.deleteRecover(<?= $story->id ?>); return false;"><?php if ($story->is_deleted): ?>Восстановить историю<?php else: ?>Удалить историю<?php endif ?></a></p>
</form>
<?php else: ?>
<h1 class="story-title1"><?= $story->titleFilled ?></h1>
<p class="story-desc"><?= $story->description_jvx ?></p>
<p>Автор истории —  <a href="<?= $user->urlProfile ?>"><?= $user->fullnameFilled ?></a></p>
<?php endif ?>
<div id="socialBlock" class="element">
<?= $this->render('/blocks/socialBlock', ['fbAppId' => Helpers::getParam('facebook/appId'), 'pageUrl' => (\yii\helpers\Url::base(true) . \Yii::$app->request->url)]) ?>
</div>
<div class="cl"></div>
<div class="yearTitle"><?= $story->yearStart ?>—<?= $story->yearEnd ?></div>
<?php foreach ($story->calendar as $day): ?>
<?php if (!empty($day['monthSwitch'])): ?><p class="hugeTitle tbo story-mounth cl"><?= $day['monthSwitch'] ?></p><?php endif ?>
	<div id="day-<?= $day['date'] ?>" class="user-photo available<?php if (!empty($day['isEmpty'])): ?> empty i-upload<?php endif ?>">
		<div class="user-photo-day"><?= $day['monthDay'] ?></div>
		<?php if (empty($day['isEmpty'])): ?>
			<?php if (empty($day['invisible'])): ?>
			<div class="user-photo-content">
				<a href="<?= $day['url'] ?>"><img src="<?= $day['image']['url'] ?>" width="<?= $day['image']['width'] ?>" height="<?= $day['image']['height'] ?>" class="user-photo-image"></a>
				<?php if ($canManage): ?><div class="user-photo-manage">Редактировать</div><?php endif ?>
				<?php if (!empty($day['isDeleted'])): ?><div class="user-photo-restore"><a class="ctrl-restore" onclick="Story.recoverMedia('<?= $day['date'] ?>')">Восстановить</a> или <a class="ctrl-replace i-upload" onclick="Story.openUpload('<?= $day['date'] ?>')">заменить</a>.</div><?php endif ?>
			</div>
			<?php endif ?>
		<?php endif ?>
	</div>
<?php endforeach ?>
<?php endif ?>
