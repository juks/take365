<?php if($confirmError): ?>
<p><?= $confirmError ?></p>
<?php else: ?>
<p>Ваша учётная запись активирована!</p>
<p><a href="<?= \yii\helpers\Url::base(true) ?>">Перейти на главную страницу</a>.</p>
<?php endif ?>
