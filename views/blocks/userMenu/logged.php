<?php
  $items = [];
  $items[] = ['title' => 'Истории', 'alias' => 'home', 'url' => $user->url];
  $items[] = ['title' => 'Профиль', 'alias' => 'profile', 'url' => $user->urlProfile];

  if ($isSubscribed) $items[] = ['title' => 'Лента', 'alias' => 'feed', 'url' => $user->urlFeed];
?>

<?php foreach ($items as $item): ?>

<?php if (!empty($this->params['isOwnPage']) && !empty($this->params['pageType']) && $this->params['pageType'] == $item['alias']): ?>
  <li class="nav-item selected"><?= $item['title'] ?></li>
<?php else: ?>
  <li class="nav-item">
    <a href="<?= $item['url'] ?>" class="nav-link"><?= $item['title'] ?></a>
  </li>
<?php endif ?>
<?php endforeach ?>

<li class="nav-item">
  <a href="#" onclick="logout();return false" class="nav-link">Выйти</a>
</li>
