
<?php if (!empty($this->params['pageType']) && $this->params['pageType'] == 'home'): ?>
<li class="active">Истории</li>
<?php else: ?>
<li<?php if (!empty($this->params['pageType']) && $this->params['pageType'] == 'story'): ?> class="active"<?php endif ?>>
  <a href="<?= $user->url ?>">Истории</a>
</li>
<?php endif ?>
<?php if (!empty($this->params['pageType']) && $this->params['pageType'] == 'profile'): ?>
<li class="active">Профиль</li>
{{ ELSE }}
<?php else: ?>
<li>
  <a href="<?= $user->urlProfile ?>">Профиль</a>
</li>
<?php endif ?>
<li>
  <a href="#" onclick="doLogin(false);return false">Выйти</a>
</li>
