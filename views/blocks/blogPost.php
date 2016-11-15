<section class="content blog">
  <article class="article-blog article-blog-full">
    <header class="article-header">
      <!-- <time datetime="2016-09-30 20:00">30.09.2016</time> -->
      <h2><?= $post->title ?></h2>
    </header>
    <?= $post->body_jvx ?>
    <footer class="article-footer">
      <a href="<?= $post->author->url ?>"><?= $post->author->username ?></a>
    </footer>
  </article>
</section>
