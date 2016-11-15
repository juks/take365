<section class="content about">
  <article class="article">
    <div class="video"><iframe src="//player.vimeo.com/video/83113874?portrait=0" width="650" height="366" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
    <h2>Идея</h2>
    <p><strong>take<span class="color">365</span></strong> &mdash;&nbsp;проект-испытание. Миниатюрный, созданный небольшой группой беззаботных энтузиастов, он&nbsp;позволяет любому человеку, находящемуся в&nbsp;бодром расположении духа, проверить свою способность вести фотолетопись в&nbsp;течение года в&nbsp;режиме &laquo;одна фотография каждый день&raquo;.</p>
    <p>Для каждой истории ведётся статистика, прилагается удобный инструмент для поддержания актуальности календаря. Кроме этого, мы разрабатываем мобильные приложения проекта. Уже сейчас все желающие могут воспользоваться приложением для устройств, работающих под управлением <a href="https://itunes.apple.com/ru/app/take365/id1082676900">iOS</a> и <a href="https://play.google.com/store/apps/details?id=org.take365.take365">Android</a>. Мы стараемся сделать всё для того, чтобы непростая цель не&nbsp;выглядела такой уж&nbsp;неприступной.</p>
    <div class="download-app">
      <a href="https://itunes.apple.com/ru/app/take365/id1082676900"><img src="/i/appstore.svg" alt="Download on the App Store"></a>
      <a href='https://play.google.com/store/apps/details?id=org.take365.take365&utm_source=global_co&utm_medium=prtnr&utm_content=Mar2515&utm_campaign=PartBadge&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1'><img src="/i/googleplay.svg" alt="Get it on Google Play"></a>
    </div>
    <h2>Зачем это нужно?</h2>
    <p>Разные люди находят в&nbsp;этом разный смысл. Для кого-то это испытание, вызов собственной непостоянности и&nbsp;творческим способностям. Для других&nbsp;&mdash; это, пожалуй, не&nbsp;самый плохой способ создать и&nbsp;сохранить визуальную летопись года своей жизни, понять, что&nbsp;же в&nbsp;это время происходило и&nbsp;что вокруг менялось.</p>
    <p>Точно можно сказать одно: это занятие настолько сильно способствует развитию умения фотографировать, чем угодно, когда угодно, видеть сюжет даже там, где его вроде&nbsp;бы и&nbsp;нет, делать больше даже при неважном настроении, что вряд&nbsp;ли многие будут жалеть о&nbsp;потраченном времени, глядя на&nbsp;то, что получилось.</p>
    <h2>Это вообще возможно?</h2>
    <p>Да, возможно! Сотни счастливчиков нашли способ организовать себя в&nbsp;непростом, но&nbsp;стоящем деле. Мы составили <a href="/howto/">несколько советов</a> &laquo;Как правильно подойти к&nbsp;делу и&nbsp;не&nbsp;разменяться на&nbsp;ерунду&raquo;.</p>
    <?php if($sampleStories): ?>
    <h3>Вот несколько случайных примеров завершённых историй</h3>
    <div class="story-random">
      <?php foreach($sampleStories as $story): ?>
        <div class="story">
          <div class="story-content">
            <div class="story-header">
              <h4><a href="<?= $story->url ?>"><?= $story->titleFilled ?></a></h4>
            </div>
            <div class="story-matrix">
              <?php foreach($story['images'] as $day): ?>
              <a href="<?= $day->urlDay ?>" class="story-matrix-item"><img src="<?= $day['t']['squareCrop'][100]['url'] ?>" alt=""></a>
              <?php endforeach ?>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
    <?php endif ?>
    <h2>Правила</h2>
    <p>Степень обязательности строго не&nbsp;определена и&nbsp;остаётся на&nbsp;усмотрение авторов историй. За&nbsp;то, когда была сделана фотография отвечают только они, они&nbsp;же решают, уместно&nbsp;ли заполнить день той фотографией, которая наиболее хорошо передаёт его настроение, но&nbsp;была сделана пару дней назад. Есть только два условия&nbsp;&mdash; авторство на&nbsp;все работы должно принадлежать автору истории и&nbsp;все фотографии должны быть сделаны во&nbsp;время этих самых 365-ти дней.</p>
    <p><a href="/#2" onclick="$('.content').moveTo(REGISTRATION_INDEX);return false">Это проще, чем кажется!</a></p>
    <h2>Чьих это рук дело?</h2>
    <p>Четыре приятеля: <a href="http://juks.ru">Игорь</a>, <a href="http://lusever.ru">Паша</a>, <a href="http://ioracle.ru">Серёжа</a> и <a href="http://evgenii.useless.rocks">Женя</a> сделали этот проект в&nbsp;своё свободное время.</p>
  </article>
</section>
