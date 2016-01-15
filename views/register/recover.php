<h1>Восстановление пароля</h1>
<form action="" onsubmit="Recover.onSubmit(event)" class="form form-register">
  <fieldset>
    <input name="email" type="text" placeholder="Ваш e-mail" autofocus>
  </fieldset>
  <fieldset>
    <div class="captcha">
      <img src="/captcha/" id="captcha-img" alt="А вот и капча" onclick="this.src='/captcha/?'+Math.random()">
    </div>
    <div class="captcha">
      <input name="captcha" type="text" id="captcha" maxlength="6" placeholder="Код" value="">
    </div>
    <p class="hint"><a href="javascript:void(document.getElementById('captcha-img').src='/captcha/?'+Math.random())">Не&nbsp;могу разобрать код</a></p>
  </fieldset>
  <fieldset>
    <input type="submit" value="Отправить">
    <a href="/" class="cancel">Отмена</a>
  </fieldset>
</form>
<!-- <div id="recoverFormHolder" class="halfWide">
<div id="recoverMessage" class="error">&nbsp;</div>
<form name="recoverForm" onsubmit="passwordRecover(); return false;">
<div class="fTitle"><span class="ro">Попробуем починить Ваш пароль. Для начала, введите адрес вашей электронной почты:</span><span id="emailMessage">&nbsp;</span></div>
<div class="fElem"><input name="email" id="email" type="text" maxlength="255" value="{{ $email }}" class="fMegaField"></div>

<div class="fTitle"><span class="ro">И 6 символов с картинки снизу:</span><span id="captchaMessage"></span></div>
<div class="fElem"><input name="captcha" id="captcha" type="text" maxlength="255" class="fMegaField"></div>

<div class="fGap">&nbsp;</div>

<div class="fElem">
<img src="/captcha/" id="captchaImage" alt="6 символов с картинки"/><br/>
<a href="#" onclick="document.getElementById('captchaImage').src = '/captcha/?' + Math.random();" class="small">Не могу разобрать код</a>
</div>

<div class="fElem"><span class="ro"><input type="submit" value="Отправить" class="fSubmit"></span><span><input type="submit" value="Пожалуй, не стоит" class="button" onclick="window.location='/'; return false;"></div>
</form>
</div> -->