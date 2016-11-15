<?php

\app\assets\BlogAsset::register($this);

?>

<?php if ($posts): ?>
    <?php foreach($posts as $post): ?>
        <?= $this->render('//blocks/blogPost', ['post' => $post]); ?>
    <?php endforeach ?>
<?php else: ?>
  <article class="article article-blog">
    <header class="article-header">
      <time datetime="2016-09-30 20:00">30.09.2016</time>
      <h2><a href="#">Лавров отказался извиняться за «Боинг»</a></h2>
    </header>
    <p>Глава МИД России Сергей Лавров отказался извиняться за гибель «Боинга» на востоке Украины в 2014 году. Соответствующее заявление он сделал во время разговора с ведущим «Би-би-си» Стивеном Сакуром.</p>
    <p>Журналист поинтересовался, не намерен ли министр извиниться в связи с обнародованием промежуточного результата расследования международной следственной группы. «Принести извинения за что?» — переспросил Лавров. Глава МИД объяснил, что Москва для начала дождется обнародования окончательных результатов, поскольку эксперты пока даже не могут назвать имена подозреваемых.</p>
    <footer class="article-footer">
      <a href="#" class="article-link-more">Читать полностью</a>
    </footer>
  </article>
  <article class="article article-blog">
    <header class="article-header">
      <time datetime="2016-09-13 20:00">13.09.2016</time>
      <h2 class="article-title"><a href="#">Боксера Фьюри уличили в употреблении кокаина</a></h2>
    </header>
    <p>Британский боксер-тяжеловес, чемпион мира по версиям Всемирной боксерской ассоциации (WBA) и Всемирной боксерской организации (WBO) Тайсон Фьюри сдал положительную допинг-пробу на кокаин. Об этом сообщает Daily Mail в пятницу, 30 сентября.</p>
    <footer class="article-footer">
      <a href="#" class="article-link-more">Читать полностью</a>
    </footer>
  </article>
  <article class="article article-blog">
    <header class="article-header">
      <time datetime="2016-09-13 20:00">11.09.2016</time>
      <h2 class="article-title"><a href="#">Россия возглавила Совбез ООН</a></h2>
    </header>
    <p>Москва с 1 октября приняла председательство в Совете Безопасности ООН. Как сообщает ТАСС, российская делегация будет руководить его работой на время обострения сирийского кризиса, а также на решающей стадии выборов нового генерального секретаря организации.</p>
    <footer class="article-footer">
      <a href="#" class="article-link-more">Читать полностью</a>
    </footer>
  </article>
  <article class="article article-blog">
    <header class="article-header">
      <time datetime="2016-09-13 20:00">02.09.2016</time>
      <h2 class="article-title"><a href="#">В Мариуполе произошел скандал из-за школьного стихотворения про флаг России</a></h2>
    </header>
    <p>Волонтер из Мариуполя Маша Украинская устроила разбирательства из-за школьного учебника, в котором, по ее словам, содержится стихотворение, прославляющее российскую государственную символику. Фотографии из книги она разместила на своей странице в Facebook.</p>
    <footer class="article-footer">
      <a href="#" class="article-link-more">Читать полностью</a>
    </footer>
  </article>
  <article class="article article-blog">
    <header class="article-header">
      <time datetime="2016-09-13 20:00">21.08.2016</time>
      <h2 class="article-title"><a href="#">Apple оштрафовали на 300 миллионов долларов за нарушение патентных прав</a></h2>
    </header>
    <p>Федеральный окружной суд штата Техас, США, обязал компанию Apple выплатить более 302 миллионов долларов в качестве компенсации за незаконное использование технологий компании VirnetX. Об этом сообщает Reuters.</p>
    <footer class="article-footer">
      <a href="#" class="article-link-more">Читать полностью</a>
    </footer>
  </article>
<?php endif ?>
