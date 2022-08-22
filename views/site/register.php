<?php

\app\assets\RegisterAsset::register($this);

?>

<main class="main">
  <section class="content">
    <header class="content-header">
      <h1 class="content-title">Регистрация</h1>
    </header>
    <form action="/api/user/register" method="post" onsubmit="Register.onSubmit(event)" class="form">
      <fieldset class="form-field">
        <input name="email" type="text" placeholder="Электронный адрес">
      </fieldset>
      <fieldset class="form-field">
        <input name="password" type="password" placeholder="Пароль" maxlength="32">
        <p class="form-hint-text">Пароль должен содержать не менее 6 символов.</p>
      </fieldset>
      <fieldset class="form-field">
        <input name="username" id="username" type="text" placeholder="Имя учётной записи">
        <p class="form-hint-text">Имя пользователя можно выбрать или изменить и после регистрации. Допускаются латинские буквы, цифры и некоторые специальные символы.</p>
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
        <input type="submit" value="Зарегистрироваться">
      </fieldset>
    </form>
  </section>
</main>
