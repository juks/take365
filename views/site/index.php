<?php

use yii\widgets\ActiveForm;

?>
<header class="header">
    <a href="javascript:void $('.content').moveTo(1)">
      <h1 class="header-logo">take365</h1>
    </a>
    <ul class="header-nav">
      <li><a href="#" onclick="Auth.open(event);return false">Вход</a></li>
      <li class="header-nav-register"><a href="/#2" onclick="$('.content').moveTo(REGISTRATION_INDEX);return false">Регистрация</a></li>
    </ul>
  </header>
  <div class="content">
    <section>
      <article class="article intro">
        <p><span>Этот проект претворяет в жизнь идею «365 фотографий».</span></p>
        <p>
          <span>Суть проста: если вы чувствуете в себе силу попытаться довести от начала до конца</span><br>
          <span>историю в формате 365, в течение целого года делать по одному снимку</span><br>
          <span>на каждый день, то этот небольшой сайт готов всячески помочь вам пройти</span><br>
          <span>этот нелёгкий путь.</span>
        </p>
        <p><a href="/help/">Узнать больше</a></p>
      </article>
      <div class="matrix"></div>
    </section>
    <section>
      <article class="article">
        <h2>Регистрация</h2>
        <form action="/api/user/register" method="post" onsubmit="Register.onSubmit(event)" class="form form-register">
          <fieldset>
            <input name="username" type="text" placeholder="Имя учётной записи">
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
      </article>
    </section>
  </div>
  <footer class="footer">
    <ul>
      <li>&copy; 2011&mdash;<?= date("Y") ?> Take365</li>
      <li><a href="/help/">О&nbsp;проекте</a></li>
      <li>
        <a href="http://take365.reformal.ru" onclick="Reformal.widgetOpen();return false;" onmouseover="Reformal.widgetPreload();">Отзывы и&nbsp;предложения</a>
        <script>
          var reformalOptions = { project_id: 66526, show_tab: false, project_host: "take365.reformal.ru" };
          (function() {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.async = true;
            script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
            document.getElementsByTagName('head')[0].appendChild(script); })
          ();
        </script>
      </li>
      <li><a href="mailto:bang@take365.org">bang@take365.org</a></li>
    </ul>
  </footer>
  <!--[if lte IE 9]><script src="/js/placeholders.jquery.min.js"></script><![endif]-->
