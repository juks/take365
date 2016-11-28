<section class="content profile">
  <header class="content-header">
    <h1 class="content-title"><?= $owner->fullnameFilled ?></h1>
    <?php if ($owner->thisIsMe): ?><a href="<?= $owner->urlEdit ?>" class="fa fa-pencil-square-o" title="Редактировать профиль"></a><?php endif ?>
  </header>
  <div class="profile-userpic fa fa-user">
    <div class="profile-userpic-img"<?php if ($owner->userpic): ?> style="background-image: url(<?= $owner->userpic['t']['maxSide']['500']['url'] ?>);<?php endif ?>"></div>
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
    <?php if ($owner->thisIsMe): ?>
    <div class="profile-field">
      <h3>Настройки</h3>
      <p><a href="<?= $owner->urlEdit ?>">Редактировать профиль</a><!--&nbsp;&nbsp;·&nbsp;&nbsp;<a href="{{ $urlSettingsFacebook }}">Настроить Facebook</a>--></p>
    </div>
    <?php endif ?>
  </div>
</section>
