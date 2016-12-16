<section id="post-holder-<?= $post->id ?>" class="content blog">
  <article class="article-blog article-blog-full">
    <header class="article-header">
      <span class="smartdate" data-timestamp="<?= $post->time_published ?>"></span>
      <h2><?= $post->title ?></h2>
    </header>
    <?= $post->body_jvx ?>
    <footer class="article-footer">
      <a href="<?= $post->author->url ?>"><?= $post->author->username ?></a>
      <?php if($post->canManage): ?><a href="<?= $post->urlEdit ?>">Редактировать</a> <a href="#" class="post-delete" data-id="<?= $post->id ?>">Удалить</a><?php endif ?>
    </footer>
  </article>
</section>
