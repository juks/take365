<?php if($confirmError): ?>
  <p class="text"><?= $confirmError ?></p>
  <?php else: ?>
  <p class="text">Адрес электронной почты успешно подтверждён!</p>
  <p class="text">
    <a href="<?= \yii\helpers\Url::base(true) ?>">Перейти на главную страницу</a>.
  </p>
<?php endif ?>
