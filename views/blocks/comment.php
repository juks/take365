<div class="comment level<?= $comment->levelLimited ?><?php if ($comment->isMine): ?> comment-my<?php endif ?>">
  <div class="comment-header">
    <div class="comment-user fa fa-user">
      <a href="<?= $comment->author->url ?>" class="comment-user-img"<?php if ($comment->author->userpic->url): ?> style="background-image: url(<?= $comment->author->userpic->url ?>);"<?php endif ?>></a>
    </div>
    <div class="comment-username"><a href="<?= $comment->author->url ?>"><?= $comment->author->fullNameFilled ?></a></div>
    <time class="comment-date">6 марта 2016, 15:33</time>
    <div class="comment-url"><a href="<?= $comment->url ?>" title="Ссылка на комментарий">#</a></div>
    <div class="comment-trash"><a href="#" title="Удалить комментарий" class="fa fa-trash-o"></a></div>
  </div>
  <div class="comment-text">
    <?= $comment->body_jvx ?>      
  </div>
  <div class="comment-options">
    <a href="#" class="comment-options-item">Ответить</a>
  </div>
</div>