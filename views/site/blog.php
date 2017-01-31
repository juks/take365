<?php

\app\assets\BlogAsset::register($this);

?>

<?php if ($posts): ?>
  <main class="content blog">
      <?php foreach($posts as $post): ?>
          <?= $this->render('//blocks/blogPost', ['post' => $post]); ?>
      <?php endforeach ?>
  </main>
<?php else: ?>

<?php endif ?>
