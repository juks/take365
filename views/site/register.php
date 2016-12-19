<?php

\app\assets\RegisterAsset::register($this);

?>

<main class="content">
  <section class="content-info">
    <header class="content-header">
      <h2 class="content-title">Регистрация</h2>
    </header>
    <form action="/api/user/register" method="post" onsubmit="Register.onSubmit(event)" class="form form-register">
      <fieldset>
        <input name="email" type="text" placeholder="Электронный адрес">
      </fieldset>
      <fieldset>
        <input name="password" type="password" placeholder="Пароль" maxlength="32">
        <span style="font-size: 10px; color: #AAAAAA">Пароль должен содержать не менее 6 символов</span>
      </fieldset>
      <fieldset>
        <input name="username" id="username" type="text" placeholder="Имя учётной записи">
        <span style="font-size: 10px; color: #AAAAAA">Имя пользователя можно выбрать или изменить и после регистрации. Допускаются латинские буквы, цифры и некоторые специальные символы</span>
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
</main>
