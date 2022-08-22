<?php

use app\assets\RecoverAsset;

RecoverAsset::register($this);

?>

<main class="main">
  <section class="content">
    <header class="content-header">
      <h1 class="content-title">Восстановление пароля</h1>
    </header>
    <form action="/api/user/recover" onsubmit="Recover.onSubmit(event)" class="form">
      <fieldset class="form-field">
        <input name="email" type="text" placeholder="Ваш e-mail" autofocus>
      </fieldset>
      <fieldset class="form-field form-field-captcha">
        <div class="form-captcha">
          <img src="/captcha/" id="captcha-img" alt="А вот и капча" onclick="this.src='/captcha/?'+Math.random()">
          <p class="form-hint-text">
            <a href="javascript:void(document.getElementById('captcha-img').src='/captcha/?'+Math.random())">Не&nbsp;могу разобрать код</a>
          </p>
        </div>
        <div class="form-code">
          <input name="captcha" type="text" id="captcha" maxlength="6" placeholder="Код" value="">
        </div>
      </fieldset>
      <fieldset class="form-field">
        <input type="submit" value="Отправить">
        <a href="/" class="cancel">Отмена</a>
      </fieldset>
    </form>
  </section>
</main>
