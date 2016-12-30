<?php
  $items = [];
  $items[] = ['title' => 'Мои истории', 'alias' => 'home', 'url' => $user->url];
  $items[] = ['title' => 'Профиль', 'alias' => 'profile', 'url' => $user->urlProfile];
  // $items[] = ['title' => 'Блог', 'alias' => 'blog', 'url' => $urlBlog];

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

<li class="nav-item nav-item-logout">
  <a href="#" onclick="logout();return false" class="nav-link">Выйти</a>
</li>
<li class="nav-item search">
  <div class="search-inner">
    <div id="search-container"></div>
    <i class="fa fa-search" aria-hidden="true"></i>
  </div>
</li>

<?php
  $this->registerJs("searchRender(document.getElementById('search-container'));");
?>
