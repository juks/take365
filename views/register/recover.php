<?php

use app\assets\RecoverAsset;

RecoverAsset::register($this);

?>

<main class="content">
  <section class="content-info">
    <header class="content-header">
      <h1 class="content-title">Восстановление пароля</h1>
    </header>
    <form action="/api/user/recover" onsubmit="Recover.onSubmit(event)" class="form form-register">
      <fieldset>
        <input name="email" type="text" placeholder="Ваш e-mail" autofocus>
      </fieldset>
      <fieldset>
        <div class="captcha">
          <img src="/captcha/" id="captcha-img" alt="А вот и капча" onclick="this.src='/captcha/?'+Math.random()">
        </div>
        <div class="captcha">
          <input name="captcha" type="text" id="captcha" maxlength="6" placeholder="Код" value="">
        </div>
        <p class="hint"><a href="javascript:void(document.getElementById('captcha-img').src='/captcha/?'+Math.random())">Не&nbsp;могу разобрать код</a></p>
      </fieldset>
      <fieldset>
        <input type="submit" value="Отправить">
        <a href="/" class="cancel">Отмена</a>
      </fieldset>
    </form>
  </section>
</main>
