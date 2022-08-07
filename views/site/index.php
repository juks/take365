<?php

use yii\widgets\ActiveForm;

?>

<header class="main-header">
  <a href="javascript:void $('.content').moveTo(1)">
    <div class="main-header-logo">take365</div>
    <div class="main-header-collage-wrap">
      <div class="main-header-collage">
        <img src="/i/screen.png" alt="">
      </div>
    </div>
  </a>
</header>
<nav class="main-nav">
  <ul class="main-nav-list">
    <li class="main-nav-list-item">
      <a href="#" onclick="Auth.open(event);return false">Вход</a>
    </li>
    <li class="main-nav-list-item">
      <a href="/register">Регистрация</a>
    </li>
  </ul>
</nav>
<main class="main">
  <article class="article">
    <section class="article-section">
      <h1 class="article-h1">По одной фотографии на каждый день</h1>
      <p class="article-text">Этот проект претворяет в жизнь идею «365 фотографий».</p>
      <p class="article-text">Суть проста: если вы чувствуете в себе силу попытаться довести от начала до конца историю в формате 365, в течение целого года делать по одному снимку на каждый день, то этот небольшой сайт готов всячески помочь вам пройти этот нелёгкий путь.</p>
    </section>
    <section class="article-section">
      <h2 class="article-h1">Идея</h2>
      <div class="article-video">
        <iframe src="//player.vimeo.com/video/83113874?portrait=0" width="100%" height="450" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
      </div>
      <p class="article-text"><strong>Take<span class="red-text">365</span></strong> — проект-испытание. Миниатюрный, созданный небольшой группой беззаботных энтузиастов, он позволяет любому человеку, находящемуся в бодром расположении духа, проверить свою способность вести фотолетопись в течение года в режиме «одна фотография каждый день».</p>
      <p class="article-text">Для каждой истории ведётся статистика, прилагается удобный инструмент для поддержания актуальности календаря. Мы стараемся сделать всё для того, чтобы непростая цель не выглядела такой уж неприступной.</p>
    </section>
    <section class="article-section">
      <h2 class="article-h1">Зачем это нужно?</h2>
      <p class="article-text">Разные люди находят в этом разный смысл. Для кого-то это испытание, вызов собственной непостоянности и творческим способностям. Для других — это, пожалуй, не самый плохой способ создать и сохранить визуальную летопись года своей жизни, понять, что же в это время происходило и что вокруг менялось.</p>
      <p class="article-text">Точно можно сказать одно: это занятие настолько сильно способствует развитию умения фотографировать, чем угодно, когда угодно, видеть сюжет даже там, где его вроде бы и нет, делать больше даже при неважном настроении, что вряд ли многие будут жалеть о потраченном времени, глядя на то, что получилось.</p>
    </section>
    <section class="article-section">
      <h2 class="article-h1">Это вообще возможно?</h2>
      <p class="article-text">Да, возможно! Сотни счастливчиков нашли способ организовать себя в непростом, но стоящем деле. Мы составили <a href="/help/howto/">несколько советов</a> «Как правильно подойти к делу и не разменяться на ерунду».</p>
    </section>
    <section class="article-section">
      <h2 class="article-h1">Правила</h2>
      <p class="article-text">Степень обязательности строго не определена и остаётся на усмотрение авторов историй. За то, когда была сделана фотография отвечают только они, они же решают, уместно ли заполнить день той фотографией, которая наиболее хорошо передаёт его настроение, но была сделана пару дней назад. Есть только два условия — авторство на все работы должно принадлежать автору истории и все фотографии должны быть сделаны во время этих самых 365-ти дней.</p>
      <p class="article-text"><a href="/register" class="btn">Это проще, чем кажется!</a></p>
    </section>
    <section class="article-section">
      <h2 class="article-h1">Чьих это рук дело?</h2>
      <p class="article-text">Четыре приятеля: <a href="http://juks.ru">Игорь</a>, <a href="http://lusever.ru">Паша</a>, <a href="http://ioracle.ru">Серёжа</a> и <a href="http://evgenii.useless.rocks">Женя</a> сделали этот проект в своё свободное время.</p>
    </section>
  </article>
</main>
