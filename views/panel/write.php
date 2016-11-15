<section class="content blog">
  <header class="content-header">
    <h1 class="content-title">Создать запись</h1>
  </header>
  <?php

  echo $this->render('Post', [
      'model' => $post,
  ])

  ?>
</section>
