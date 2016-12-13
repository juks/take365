<?php include('_header.php') ?>
<?php include('_greeting.php') ?>
<p>Пользователь <a href="<?= $commentAuthor->url ?>"><?= $commentAuthor->fullnameFilled ?></a> <?= $this->hUserAction($commentAuthor, 'оставил', 'оставила') ?> новый комментарий к Вашей истории «<a href="<?= $target->url ?>"><?= $target->titleFilled ?></a>»</p>
<p>Текст комментария:</p>
<?= $this->hQStart() ?><?= $comment->body_jvx ?><?= $this->hQEnd() ?>
<?php include('_footer.php') ?>