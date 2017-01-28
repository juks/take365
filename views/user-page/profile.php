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
    <?php if ($subscribed || $subscribers): ?>
    <div class="subscribers" id="subscribers">
      <?php if ($subscribed): ?>
      <h3><?= mb_convert_case($owner->genderString, MB_CASE_TITLE, "UTF-8") ?> читает</h3>
      <ul class="subscribers-list">
        <?php foreach ($subscribed as $user): ?>
        <li class="subscribers-item">
          <a href="<?= $user->urlProfile ?>" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img"<?php if (!empty($user->userpic)): ?> style="background-image: url('<?= $user->userpic['t']['maxSide'][100]['url'] ?>');"<?php endif ?>></span>
            <span class="subscribers-item-username"><?= $user->usernameFilled ?></span>
          </a>
        </li>
      <?php endforeach ?>
      </ul>
      <?php endif ?>
      <?php if ($subscribers): ?>
      <h3><?= mb_convert_case($owner->genderStringAccusative, MB_CASE_TITLE, "UTF-8") ?> читают</h3>
      <ul class="subscribers-list">
        <?php foreach ($subscribers as $subscriber): ?>
        <li class="subscribers-item">
          <a href="<?= $subscriber->urlProfile ?>" class="subscribers-item-link">
            <span class="fa fa-user"></span>
            <span class="subscribers-item-img"<?php if (!empty($subscriber->userpic)): ?> style="background-image: url('<?= $subscriber->userpic['t']['maxSide'][100]['url'] ?>');"<?php endif ?>></span>
            <span class="subscribers-item-username"><?= $subscriber->usernameFilled ?></span>
          </a>
        </li>
        <?php endforeach ?>
        </ul>
        <?php endif ?>
    </div>
    <?php endif ?>
  </section>
</main>
