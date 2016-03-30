<?php include('_header.php') ?>
<p>Пользователь <a href="<?= $commentAuthor->url ?>"><?= $commentAuthor->fullnameFilled ?></a> <?= $this->hUserAction($commentAuthor, 'ответил', 'ответила') ?> на ваш комментарий к истории «<a href="<?= $target->url ?>"><?= $target->titleFilled ?></a>»</p>
<?= $this->hQStart() ?><?= $parentComment->body_jvx ?><?= $this->hQEnd() ?>
<p>Вот, что <?= $this->hUserAction($commentAuthor, 'он', 'она') ?> <a href="<?= $comment->url ?>"><?= $this->hUserAction($commentAuthor, 'написал', 'написала') ?></a>:</p>
<?= $this->hQStart() ?><?= $comment->body_jvx ?><?= $this->hQEnd() ?>
<?php include('_footer.php') ?>