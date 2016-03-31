<header class="article-header">
  <h1 class="article-title">Профиль <a href="<?= $owner->url ?>"><?= $owner->fullnameFilled ?></a></h1>
  <?php if ($owner->thisIsMe): ?><a href="<?= $owner->urlEdit ?>" class="fa fa-pencil-square-o" title="Редактировать профиль"></a><?php endif ?>
</header>
<section class="profile">
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
      <p><a href="<?= $owner->homepage ?>"><?= $owner->homepage ?></a></p>
    </div>
    <?php endif ?>
    <div class="profile-field">
      <?php if ($owner->thisIsMe): ?>
        <h3>Истории</h3>
      <p><a href="<?= $owner->url ?>">Страница моих историй</a></p>
      <?php elseif ($owner->hasStories): ?>
        <h3>Истории</h3>
      <p><a href="<?= $owner->url ?>">Истории пользователя</a></p>
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
