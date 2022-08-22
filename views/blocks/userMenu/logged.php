<?php
  $items = [];
  $items[] = ['title' => 'Мои истории', 'alias' => 'home', 'url' => $user->url];
  $items[] = ['title' => 'Профиль', 'alias' => 'profile', 'url' => $user->urlProfile];
  // $items[] = ['title' => 'Блог', 'alias' => 'blog', 'url' => $urlBlog];

  if ($isSubscribed) $items[] = ['title' => 'Лента', 'alias' => 'feed', 'url' => $user->urlFeed];
?>

<?php foreach ($items as $item): ?>

<?php if (!empty($this->params['isOwnPage']) && !empty($this->params['pageType']) && $this->params['pageType'] == $item['alias']): ?>
  <li class="main-nav-list-item selected"><?= $item['title'] ?></li>
<?php else: ?>
  <li class="main-nav-list-item">
    <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
  </li>
<?php endif ?>
<?php endforeach ?>

<li class="main-nav-list-item">
  <a href="#" onclick="logout();return false">Выйти</a>
</li>
<?php include('searchBlock.php'); ?>

<?php
  $this->registerJs("searchRender(document.getElementById('search-container'));");
?>
