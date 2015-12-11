{{ BEGIN jsInit }}
initStory();

$('.notifyTip').tooltipsy({
	offset: [0, 1],
	delay: 200
});
{{ END jsInit }}

<?php
use app\assets\StoryAsset;

StoryAsset::register($this);
?>

<?php if ($story): ?>
<?php foreach ($story->calendar as $day): ?>
<?php if (!empty($day['monthSwitch'])): ?><p class="hugeTitle tbo story-mounth cl">Месяц</p><?php endif ?>
	<div id="day-{{ $yearIndex }}-{{ $monthIndex}}-{{ $monthDay }}" class="user-photo available{{ UNLESS image }} empty i-upload{{ END }}">
		<div class="user-photo-day"><?= $day['monthDay'] ?></div>
		<?php if (empty($day['imvisible'])): ?>
		<div class="user-photo-content">
			<a href="{{ $urlDay }}"><img src="<?= $day['image']['url'] ?>" width="<?= $day['image']['width'] ?>" height="<?= $day['image']['height'] ?>" class="user-photo-image"></a>
			<?php if ($canManage): ?><div class="user-photo-manage">Редактировать</div><?php endif ?>
			<?php if (!empty($day['isDeleted'])): ?><div class="user-photo-restore"><a class="ctrl-restore" onclick="Story.recoverMedia('{{ $yearIndex }}-{{ $monthIndex}}-{{ $monthDay }}')">Восстановить</a> или <a class="ctrl-replace i-upload" onclick="Story.openUpload('{{ $yearIndex }}-{{ $monthIndex}}-{{ $monthDay }}')">заменить</a>.</div><?php endif ?>
		</div>
		<?php endif ?>
	</div>
<?php endforeach ?>
<?php endif ?>
