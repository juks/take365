<article id="post-holder-<?= $post->id ?>" class="article">
  <header class="article-header">
    <time class="smartdate" data-timestamp="<?= $post->time_published ?>"></time>
    <h2><?= $post->title ?></h2>
  </header>
  <?= $post->body_jvx ?>
  <footer class="article-footer">
    <a href="<?= $post->author->url ?>" class="article-author"><?= $post->author->username ?></a>
    <?php if($post->canManage): ?>
      <span class="article-options">
        <a href="<?= $post->urlEdit ?>" class="post-edit"><i class="fa fa-pencil" aria-hidden="true"></i> Редактировать</a> <a href="#" class="post-delete" data-id="<?= $post->id ?>"><i class="fa fa-times" aria-hidden="true"></i> Удалить</a>
      </span>
    <?php endif ?>
  </footer>
</article>
