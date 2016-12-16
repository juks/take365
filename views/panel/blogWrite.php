<section class="content blog">
  <header class="content-header">
    <h1 class="content-title"><?= $formTitle ?></h1>
  </header>
  <?php
  \app\assets\PanelBlogAsset::register($this);

  echo $this->render('PostForm', [
      'post' => $post,
  ]);

  ?>
</section>
