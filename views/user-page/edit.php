<?php

use app\assets\ProfileAsset;

ProfileAsset::register($this);

?>

<header class="article-header">
	<h1 class="article-title">Редактирование профиля</h1>
</header>
<ul class="profile-nav">
	<li id="mainSwHolder" class="fl ro bbo roundedSw"><a href="#" id="mainLink" onclick="switchSection('main'); return false;" class="simple noOl" title="Редактирование анкеты">Основное</a></li>
	<li id="imagesSwHolder" class="fl ro bbo"><a href="#" id="imagesLink" onclick="switchSection('images'); return false;" title="Загрузка изображений" class="noOl">Изображения</a></li>
	<!-- <li id="contactsSwHolder" class="fl ro bbo hidden"><a href="#" id="contactsLink" onclick="switchSection('contacts'); return false;" title="Изменение контактной информации" class="noOl">Ресурсы и контакты</a></li> -->
	<li id="secSwHolder" class="fl ro bbo"><a href="#" id="secLink" onclick="switchSection('sec'); return false;" title="Изменение пароля и параметров безопасности" class="noOl">Безопасность</a></li>
</ul>
<div id="mainHolder">
	<form action="#" name="mainForm" id="mainForm" onsubmit="updateProfile('mainForm'); return false;" class="form">
		<input type="hidden" name="id" value="<?= $user->id ?>">
		<div id="mainFormDefaultMessage" class="small error">&nbsp;</div>
		<div class="fTitle"><label for="email" id="emailLabel">Меня зовут</label></div>
		<div class="fElem"><input type="text" name="fullname" id="fullname" value="<?= $user->fullname ?>" class="halfWide" maxlength="255" /></div>
		<div class="fTitle"><label for="email" id="emailLabel">Email</label></div>
		<div class="fElem"><input type="text" name="email" id="email" value="<?= $user->email ?>" class="halfWide" maxlength="255" /></div>
		<div class="fTitle"><label for="username" id="usernameLabel">Пользователь</label></div>
		<div class="fElem"><input type="text" name="username" id="username" value="<?= $user->username ?>" class="halfWide" maxlength="255" /></div>
		<div class="fTitle">Описание</div>
		<div class="fElem"><textarea name="description" class="halfWide" style="height: 15em"><?= $user->description ?></textarea></div>
		<div class="fTitle">Адрес моего сайта или просто сайта про меня</div><div class="fElem"><input type="text" name="url" value="<?= $user->homepage ?>" class="halfWide" maxlength=255 /></div>
		<div class="fTitle">Пол</div>
		<div class="fElem">
			<select name='sex' id='genderSelect'>
				<option value="0"<?php if($user->sexTitle == 'undefined'): ?> selected<?php endif ?>>Сомнительный</option>
				<option value="1"<?php if($user->sexTitle == 'male'): ?> selected<?php endif ?>>Мужской</option>
				<option value="2"<?php if($user->sexTitle == 'female'): ?> selected<?php endif ?>>Женский</option>
			</select>
		</div>
		<div class="fElem">
			<input type="submit" class="fSubmit" name="submitButton" value="Сохранить" />
		</div>
	</form>
</div>
<div id="imagesHolder" class="hidden">

<div class="fileDrag" id="userPhotoDrop">
	<h2 class="mediumTitle">Фотография</h2>
	<div class="element">
		<div>
		<?php if ($user->userpic): ?>
			<img id="userPhoto" src="<?= $user->userpic['t']['maxSide']['500']['url'] ?>" width="<?= $user->userpic['t']['maxSide']['500']['width'] ?>" height="<?= $user->userpic['t']['maxSide']['500']['height'] ?>" />
			<div id="userPhotoDelete"><a href="javascript:;" onclick="deleteMedia(<?= $user->userpic->id ?>,'userPhoto')">удалить</a></div>
		<?php else: ?>
			<div id="userPhoto"></div>
			<div id="userPhotoDelete" class="hidden"><a href="javascript:;">удалить</a></div>
		<?php endif ?></div>
	</div>
	<form name="userPhotoUpload" method="post" action="/ajax/media/upload" enctype="multipart/form-data">
		<input name="targetId" value="<?= $targetId ?>" type="hidden">
		<input name="targetType" value="<?= $targetType ?>" type="hidden">
		<input name="mediaType" value="<?= $mediaType ?>" type="hidden">
		<p id="userPhotoUploadWrap"><a id="userPhotoPick" href="javascript:;">Загрузить</a> фотографию.</p>
	</form>
	</div>
</div>
<div id="secHolder" class="hidden">
	<table border="0">
		<tr>
			<td width="500">
				<form name="secForm" id="secForm" onsubmit="updateProfile('secForm'); return false;" class="form">
					<div id="secFormDefaultMessage" class="small error">&nbsp;</div>
					<input type="hidden" name="id" value="<?= $targetId ?>">
					<div class="fTitle to">
						<label for="password" id="passwordLabel">Новый пароль:</label>
					</div>
					<div class="fElem">
						<input type="password" name="password" id="password" class="fMegaField" maxlength="20" onkeyup="passwordStrength()"/>
					</div>
					<div class="fTitle">
						<label for="password1" id="password1Label">Подтверждение пароля:</label>
					</div>
					<div class="fElem">
						<input type="password" name="password1" id="password1" class="fMegaField" maxlength="20" />
					</div>
					<input type="submit" class="fSubmit" name="submitButton" value="Сохранить" />
				</form>
			</td>
			<td width="5"> </td>
			<td>
				<div id="ps" class="small strong" style="padding: 15px;"></div>
			</td>
		</tr>
	</table>
</div>
