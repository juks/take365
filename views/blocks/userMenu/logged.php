
<?php
  $items = [];
  $items[] = ['title' => 'Истории', 'alias' => 'home', 'url' => $user->url];
  $items[] = ['title' => 'Профиль', 'alias' => 'profile', 'url' => $user->urlProfile];

  if ($isSubscribed) $items[] = ['title' => 'Лента', 'alias' => 'feed', 'url' => $user->urlFeed];

?>

<?php foreach ($items as $item): ?>

<?php if (!empty($this->params['isOwnPage']) && !empty($this->params['pageType']) && $this->params['pageType'] == $item['alias']): ?>
  <li class="active"><?= $item['title'] ?></li>
<?php else: ?>
  <li>
    <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
  </li>
<?php endif ?>
<?php endforeach ?>


<li>
  <a href="#" onclick="logout();return false">Выйти</a>
</li>
