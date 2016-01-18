<?php
use app\assets\RecoverAsset;

RecoverAsset::register($this);
?>

<h1>Восстановление пароля</h1>

<?php if ($recoverError): ?>
<p><?= $recoverError ?></p>
<p>Если вам всё ещё необходимо восстановить пароль, пожалуйста, повторите <a href="/register/recover/">процедуру восстановления</a> заново.<br>Срок годности кода — 2 часа.</p>
<?php else: ?>

<form action="/api/user/recover-update" onsubmit="RecoverUpdate.onSubmit(event)" class="form form-register">
  <fieldset>
    <input oninput="RecoverUpdate.onPassword(event)" name="password" type="password" placeholder="Ваш новый пароль" autofocus>
  </fieldset>
  <fieldset>
    <input oninput="RecoverUpdate.onPassword(event)" name="password" type="password" placeholder="Повторите ваш новый пароль">
  </fieldset>
  <input name="code" type="hidden" value="<?php $code ?>">
  <input name="id" type="hidden" value="<?php $userId ?>">
  <fieldset>
    <input type="submit" value="Отправить">
    <a href="/" class="cancel">Отмена</a>
  </fieldset>
</form>

<?php endif ?>

<!-- <div id="recoverSuccess" class="hidden">Письмо с инструкциями выслано на ваш почтовый адрес.</div>
<div id="updateSuccess" class="hidden">Ваш пароль изменён. Теперь вы можете перейти на <a href="/login/">страницу входа</a>.</div> -->
