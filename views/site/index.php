<?php

use yii\widgets\ActiveForm;

?>

<header class="header">
  <h1 class="header-logo">
    <a href="javascript:void $('.content').moveTo(1)">take365</a>
    <!-- <sup class="header-logo-text">блог</sup> -->
  </h1>
</header>
<nav class="nav">
  <ul class="nav-list">
    <li class="nav-item">
      <a href="#" onclick="Auth.open(event);return false" class="nav-link">Вход</a>
    </li>
    <li class="nav-item">
      <a href="/register" class="nav-link nav-link-register">Регистрация</a>
    </li>
  </ul>
</nav>
<div class="matrix"></div>
<main class="content intro">
  <article class="article">
    <p><span>Этот проект претворяет в жизнь идею «365 фотографий».</span></p>
    <p>
      <span>Суть проста: если вы чувствуете в себе силу попытаться довести от начала до конца</span><br>
      <span>историю в формате 365, в течение целого года делать по одному снимку</span><br>
      <span>на каждый день, то этот небольшой сайт готов всячески помочь вам пройти</span><br>
      <span>этот нелёгкий путь.</span>
    </p>
    <p><a href="/help/">Узнать больше</a></p>
  </article>
</main>
