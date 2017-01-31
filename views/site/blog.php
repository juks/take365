<?php

\app\assets\BlogAsset::register($this);

?>

<main class="content blog">
  <a href="/panel/post-write" class="blog-create"><i class="fa fa-plus" aria-hidden="true"></i> Создать запись</a>

  <?php if ($posts): ?>
    <?php foreach($posts as $post): ?>
      <?= $this->render('//blocks/blogPost', ['post' => $post]); ?>
    <?php endforeach ?>

  <?php else: ?>
    <article class="article article-blog">
      <header class="article-header">
        <h2>Нет записей</h2>
      </header>
    </article>
  <?php endif ?>
</main>
