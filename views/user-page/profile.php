<?php

use app\assets\ProfileAsset;

ProfileAsset::register($this);

?>

<header class="article-header">
  <h1 class="article-title">Профиль <a href="<?= $user->url ?>"><?= $user->fullnameFilled ?></a></h1>
  <?php if ($user->thisIsMe): ?><a href="<?= $user->urlEdit ?>" class="fa fa-pencil-square-o" title="Редактировать профиль"></a><?php endif ?>
</header>
<section class="profile">
  <div class="profile-userpic fa fa-user">
    <div class="profile-userpic-img"<?php if ($user->userpic): ?> style="background-image: url(<?= $user->userpic['t']['maxSide']['500']['url'] ?>);<?php endif ?>"></div>
  </div>
  <div class="profile-desc">
    <div class="profile-field">
      <h3>О себе</h3>
      <?php if ($user->description_jvx): ?><p><?= $user->description_jvx ?></p><?php else: ?><p>Пользователь ничего не указал о себе</p><?php endif ?>
    </div>
    <?php if ($user->homepage): ?>
    <div class="profile-field">
      <h3>Контактная информация</h3>
      <p><a href="<?= $user->homepage ?>"><?= $user->homepage ?></a></p>
    </div>
    <?php endif ?>
    <div class="profile-field">
      <?php if ($user->thisIsMe): ?>
        <h3>Истории</h3>
      <p><a href="<?= $user->url ?>">Страница моих историй</a></p>
      <?php elseif ($user->hasStories): ?>
        <h3>Истории</h3>
      <p><a href="<?= $user->url ?>">Истории пользователя</a></p>
      <?php endif ?>
    </div>
    <?php if ($user->thisIsMe): ?>
    <div class="profile-field">
      <h3>Настройки</h3>
      <p><a href="<?= $user->urlEdit ?>">Редактировать профиль</a><!--&nbsp;&nbsp;·&nbsp;&nbsp;<a href="{{ $urlSettingsFacebook }}">Настроить Facebook</a>--></p>
    </div>
    <?php endif ?>
  </div>
</section>
