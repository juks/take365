<?php
use app\ext\AuthChoice;
?>
<div class="auth-popup popup-wrap" style="display: none;" onclick="event.target===this&&Auth.close(event)">
    <div class="popup">
      <h2 class="popup-title">Точка входа</h2>
      <form action="/api/auth/login" onsubmit="AuthForm.onSubmit(event)" class="form form-auth">
        <fieldset class="form-field">
          <input name="username" type="text" placeholder="Пользователь">
        </fieldset>
        <fieldset class="form-field">
          <input name="password" type="password" placeholder="Пароль">
        </fieldset>
        <fieldset class="form-field">
          <label class="form-label form-checkbox">
            <input type="checkbox" name="rememberMe" value="1" checked>
            <span>Запомнить</span>
          </label>
        </fieldset>
        <fieldset class="form-field form-field-footer">
          <?php $authAuthChoice = AuthChoice::begin([
              'baseAuthUrl' => ['site/auth'],
              'popupMode' => true,
          ]); ?>
            <input type="submit" value="Войти">
              <span class="auth-clients-sep">или</span>
              <?php foreach ($authAuthChoice->getClients() as $client): ?>
                <?php $authAuthChoice->clientLink($client) ?>
              <?php endforeach; ?>
          <?php AuthChoice::end(); ?>
        </fieldset>
        <p class="form-text">
          <a href="/register/recover/">Забыли пароль</a> или ещё не <a href="/register">зарегистрировались</a>?
        </p>
      </form>
      <div class="popup-close" title="Закрыть" onclick="Auth.close(event)">
        <i class="fa-solid fa-xmark"></i>
      </div>
    </div>
</div>
