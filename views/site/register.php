<?php

\app\assets\RegisterAsset::register($this);

?>

<section class="content register">
  <header class="content-header">
    <h2 class="content-title">Регистрация</h2>
  </header>
  <form action="/api/user/register" method="post" onsubmit="Register.onSubmit(event)" class="form form-register">
    <fieldset>
      <input name="username" type="text" placeholder="Имя учётной записи">
      <span style="font-size: 11px; color: #BBBBBB">Можно использовать латинские символы и цифры, а между ними &mdash; чёрточку</span>
    </fieldset>
    <fieldset>
      <input name="email" type="text" placeholder="Электронный адрес">
    </fieldset>
    <fieldset>
      <input name="password" type="password" placeholder="Пароль" maxlength="32">
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
      <input type="submit" value="Зарегистрироваться">
    </fieldset>
  </form>
</section>