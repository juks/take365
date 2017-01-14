<main class="content profile">
  <section class="content-info">
    <header class="content-header">
      <h1 class="content-title"><?= $owner->fullnameFilled ?></h1>
    </header>
    <div class="profile-userpic">
      <div class="fa fa-user">
        <div class="profile-userpic-img"<?php if ($owner->userpic): ?> style="background-image: url(<?= $owner->userpic['t']['maxSide']['500']['url'] ?>);<?php endif ?>"></div>
      </div>
      <?php if ($owner->thisIsMe): ?>
        <a href="<?= $owner->urlEdit ?>">Редактировать профиль</a>
      <?php endif ?>
    </div>
    <div class="profile-desc">
      <div class="profile-field">
        <h3>О себе</h3>
        <?php if ($owner->description_jvx): ?><p><?= $owner->description_jvx ?></p><?php else: ?><p>Пользователь ничего не указал о себе</p><?php endif ?>
      </div>
      <?php if ($owner->homepage): ?>
      <div class="profile-field">
        <h3>Контактная информация</h3>
        <p><a href="<?= $homepageUrl ?>"><?= $owner->homepage ?></a></p>
      </div>
      <?php endif ?>
      <div class="profile-field">
        <?php if ($owner->thisIsMe): ?>
          <h3>Истории</h3>
        <p><a href="<?= $owner->url ?>">Страница моих историй</a></p>
        <?php else: ?>
          <h3>Истории</h3>
        <p><a href="<?= $owner->url ?>">Страница пользователя</a></p>
        <?php endif ?>
      </div>
    </div>
    <div class="subscribers">
      <h3>Его подписчики</h3>
      <ul class="subscribers-list">
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
      </ul>
      <h3>Он подписан</h3>
      <ul class="subscribers-list">
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/1f/19741/resume_100x100.jpg');"></span>
            <span class="subscribers-item-username">Игорь</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/47/10246/_6022015_100x64.jpg');"></span>
            <span class="subscribers-item-username">Mare d'amore</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img" style="background-image: url('https://take365.org/media/thumbs/p2/userpic/8c/12870/2014-08-22_06.43.29_1_100x100.jpg');"></span>
            <span class="subscribers-item-username">manka</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">Lefych</span>
          </a>
        </li>
        <li class="subscribers-item">
          <a href="#" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-username">julik</span>
          </a>
        </li>
      </ul>
    </div>
  </section>
</main>
