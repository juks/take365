<?php

use app\assets\ProfileAsset;

ProfileAsset::register($this);

?>

<main class="content profile profile-edit">
  <section class="content-info">
  	<header class="content-header">
      <a href="<?= $owner->urlProfile ?>" title="Вернутся в профиль" class="fa fa-long-arrow-left"></a>
      <h1 class="content-title">Редактирование профиля</h1>
    </header>
    <div class="profile-content">
      <form name="userPhotoUpload" method="post" action="/ajax/media/upload" enctype="multipart/form-data">
        <input name="targetId" value="<?= $targetId ?>" type="hidden">
        <input name="targetType" value="<?= $targetType ?>" type="hidden">
        <input name="mediaType" value="<?= $mediaType ?>" type="hidden">
        <div id="userPhotoDrop" class="profile-userpic fa fa-user">
          <?php if ($owner->userpic): ?>
            <div id="userPhoto" class="profile-userpic-img"<?php if ($owner->userpic): ?> style="background-image: url(<?= $owner->userpic['t']['maxSide']['500']['url'] ?>);<?php endif ?>">
              <a id="userPhotoPick" href="javascript:;" class="profile-userpic-edit">Редактировать</a>
            </div>
            <div id="userPhotoDelete" class="profile-photo-remove">
              <a href="javascript:;" title="Удалить" class="fa fa-trash-o" onclick="deleteMedia(<?= $owner->userpic->id ?>,'userPhoto')"></a>
            </div>
          <?php else: ?>
            <div id="userPhoto" class="profile-userpic-img">
              <a id="userPhotoPick" href="javascript:;" class="profile-userpic-edit">Загрузить</a>
            </div>
            <div id="userPhotoDelete" class="profile-photo-remove hidden">
              <a href="javascript:;" title="Удалить" class="fa fa-trash-o"></a>
            </div>
          <?php endif ?>
        </div>
      </form>
    </div>
    <div class="profile-content">
      <form action="/api/user/update-profile" method="post" onsubmit="ProfileForm.onSubmit(event)" name="mainForm" id="mainForm" class="form">
        <input type="hidden" name="id" value="<?= $owner->id ?>">
        <fieldset>
          <label for="fullname" id="fullnameLabel" class="label">Меня зовут</label>
          <input type="text" name="fullname" id="fullname" value="<?= $owner->fullname ?>" maxlength="255">
        </fieldset>
        <fieldset>
          <label for="email" id="emailLabel" class="label">Email</label>
          <input type="text" name="email" id="email" value="<?= $owner->email ?>" maxlength="255">
        </fieldset>
        <fieldset>
          <label for="username" id="usernameLabel" class="label">Пользователь</label>
          <input type="text" name="username" id="username" value="<?= $owner->username ?>" maxlength="255">
        </fieldset>
        <fieldset>
          <label class="label">Описание</label>
          <textarea name="description"><?= $owner->description ?></textarea>
        </fieldset>
        <fieldset>
          <label class="label">Адрес моего сайта или просто сайта про меня</label>
          <input type="text" name="homepage" value="<?= $owner->homepage ?>" maxlength="255">
        </fieldset>
        <fieldset>
          <label class="label label-inline">Пол</label>
          <select name="sex" id="genderSelect">
            <option value="0"<?php if($owner->sexTitle == 'undefined'): ?> selected<?php endif ?>>Сомнительный</option>
            <option value="1"<?php if($owner->sexTitle == 'male'): ?> selected<?php endif ?>>Мужской</option>
            <option value="2"<?php if($owner->sexTitle == 'female'): ?> selected<?php endif ?>>Женский</option>
          </select>
        </fieldset>
        <fieldset>
          <label class="label label-inline">Часовой пояс</label>
          <select name="timezone" id="timezoneSelect">
            <?php foreach($timezones as $timezone): ?><option value="<?= $timezone['id'] ?>"<?php if($timezone['isSelected']): ?> selected<?php endif ?>><?= $timezone['title'] ?></option><?php endforeach ?>
          </select>
        </fieldset>
        <fieldset>
          <input id="optNotify" type="checkbox" value="1" name="optNotify"<?= $optNotify ? ' checked' : '' ?> hidden>
          <label for="optNotify" class="label label-inline">Получать уведомления по электронной почте</label>
        </fieldset>
        <fieldset>
          <input id="optNewsletter" type="checkbox" value="1" name="optNewsletter"<?= $optNewsletter ? ' checked' : '' ?> hidden>
          <label for="optNewsletter">Получать письма с новостями проекта</label>
        </fieldset>
        <fieldset>
          <input type="submit" name="submitButton" value="Сохранить">
        </fieldset>
      </form>
    </div>
    <div class="profile-content">
      <h2>Изменение пароля</h2>
      <form name="secForm" id="secForm" method="post" action="/api/user/update-security" onsubmit="SecForm.onSubmit(event)" class="form">
        <input type="hidden" name="id" value="<?= $targetId ?>">
        <fieldset>
          <input type="password" name="password" id="password" maxlength="20" placeholder="Новый пароль">
        </fieldset>
        <fieldset>
          <input type="password" name="password1" id="password1" maxlength="20" placeholder="Подтверждение пароля">
        </fieldset>
        <fieldset>
          <input type="submit" name="submitButton" value="Изменить пароль">
        </fieldset>
      </form>
    </div>
  </section>
</main>
