<?php
use app\ext\AuthChoice;
?>
<div class="auth-popup popup-wrap" style="display: none;" onclick="event.target===this&&Auth.close(event)">
    <div class="popup">
      <h2>Точка входа</h2>
      <form action="/api/auth/login" onsubmit="AuthForm.onSubmit(event)" class="form form-login">
        <fieldset>
          <input name="username" type="text" placeholder="Пользователь">
        </fieldset>
        <fieldset>
          <input name="password" type="password" placeholder="Пароль">
        </fieldset>
        <fieldset class="remember">
          <input type="checkbox" name="rememberMe" value="1" id="remember" checked>
          <label for="remember">Запомнить</label>
        </fieldset>
        <fieldset>
          <?php $authAuthChoice = AuthChoice::begin([
              'baseAuthUrl' => ['site/auth'],
              'popupMode' => true,
          ]); ?>
            <input type="submit" value="Войти">
            <div class="auth-links">
              <p>Или</p>
              <?php foreach ($authAuthChoice->getClients() as $client): ?>
                <?php $authAuthChoice->clientLink($client) ?>
              <?php endforeach; ?>
            </div>
          <?php AuthChoice::end(); ?>
        </fieldset>
        <fieldset class="hint">
          <p><a href="/register/recover/">Забыли пароль</a> или <a href="/register">ещё не зарегистрировались</a>?</p>
        </fieldset>
      </form>
      <span class="close" title="Закрыть" onclick="Auth.close(event)"></span>
    </div>
</div>
