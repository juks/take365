{{ BEGIN jsInit }}{{ END jsInit }}
{{ BEGIN content }}
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
	<form name="mainForm" id="mainForm" onsubmit="updateProfile('mainForm'); return false;" class="form">
		<input type="hidden" name="userId" value="{{ $id }}">
		<div id="mainFormDefaultMessage" class="small error">&nbsp;</div>
		<div class="fTitle">Меня зовут<span class="lo small" id="emailMessage">&nbsp;</span></div>
		<div class="fElem"><input type="text" name="fullName" id="fullName" value="{{ $fullname }}" class="halfWide" maxlengt="255" /></div>
		<div class="fTitle"><label for="email" id="emailLabel">Email</label></div>
		<div class="fElem"><input type="text" name="email" id="email" value="{{ $email }}" class="halfWide" maxlengt="255" /></div>
		<div class="fTitle">Описание</div>
		<div class="fElem"><textarea name="description" class="halfWide" style="height: 15em">{{ $description }}</textarea></div>
		<div class="fTitle">Адрес моего сайта или просто сайта про меня</div><div class="fElem"><input type="text" name="url" value="{{ $url }}" class="halfWide" maxlengt=255 /></div>
		<div class="fTitle">Пол</div>
		<div class="fElem">
			<select name='sex' id='genderSelect'>
				<option value="0"{{ if($isSexUndefined, ' selected') }}>Сомнительный</option>
				<option value="1"{{ if($isSexMale, ' selected') }}>Мужской</option>
				<option value="2"{{ if($isSexFemale, ' selected') }}>Женский</option>
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
		{{ IF userPhoto }}
			{{ BEGIN userPhoto }}<img id="userPhoto" src="{{$url}}" width="{{$width}}" height="{{$height}}" />{{ END }}
			<div id="userPhotoDelete"><a href="javascript:;" onclick="deleteMedia({{ $userPhotoId }},'userPhoto')">удалить</a></div>
		{{ ELSE }}
			<div id="userPhoto"></div>
			<div id="userPhotoDelete" class="hidden"><a href="javascript:;">удалить</a></div>
		{{ END }}</div>
	</div>
	<form name="userPhotoUpload" method="post" action="/ajax/media/upload" enctype="multipart/form-data">
		<input name="targetId" value="{{ $targetId }}" type="hidden">
		<input name="targetType" value="{{ $targetType }}" type="hidden">
		<input name="mediaType" value="{{ $mediaTypeUserPhoto }}" type="hidden">
		<p id="userPhotoUploadWrap"><a id="userPhotoPick" href="javascript:;">Загрузить</a> фотографию.</p>
	</form>
	</div>
</div>
<!-- <div id="contactsHolder" class="hidden">
	<div id="userContacts">
		{{ BEGIN userContacts }}
		{{ inc('users/profile/listContacts.tpl') }}
		{{ END userContacts }}
	</div>
	<div class="fTitle">Добавить</div>
	<form name="addContacts" onsubmit="addContact(); return false;">
		<div class="fElem fl ro">
			<select name="newContactId" id="newContactId" style="width: 200px">
				<option value="0">Выберите ресурс</option>
				{{ BEGIN contacts }}<option value="{{ $id }}">{{ $name }}</option>{{ END contacts }}
			</select>
		</div>
		<div class="fElem">
			<input type="text" id="newContactValue" name="contactValue" value="" style="width: 200px" maxlength="100">
		</div>
		<div class="cl"></div>
		<div class="eElem">
			<input type="button" class="fSubmit" value="Добавить" onclick="addContact(); return false;">
		</div>
	</form>
</div> -->
<div id="secHolder" class="hidden">
	<table border="0">
		<tr>
			<td width="500">
				<form name="secForm" id="secForm" onsubmit="updateProfile('secForm'); return false;" class="form">
					<div id="secFormDefaultMessage" class="small error">&nbsp;</div>
					<input type="hidden" name="userId" value="{{ $id }}">
					<div class="fTitle to">
						<label for="password" id="passwordLabel">Новый пароль:</label>
					</div>
					<div class="fElem">
						<input type="password" name="password" id="password" class="fMegaField" maxlengt="20" onkeyup="passwordStrength()"/>
					</div>
					<div class="fTitle">
						<label for="password1" id="password1Label">Подтверждение пароля:</label>
					</div>
					<div class="fElem">
						<input type="password" name="password1" id="password1" class="fMegaField" maxlengt="20" />
					</div>
					<input type="submit" class="fSubmit" name="submitButton" value="Сохранить" />
				</form>
			</td>
			<td width="5">{{ hSpacer(5,1) }}</td>
			<td>
				<div id="ps" class="small strong" style="padding: 15px;"></div>
			</td>
		</tr>
	</table>
</div>
{{ END content }}