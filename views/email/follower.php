<?php include('_header.php') ?>
<?php include('_greeting.php') ?>
Пользователь <a href="<?= $reader->url ?>"><?= $reader->fullnameFilled ?></a> <?= $this->hUserAction($reader, 'подписался', 'подписалась') ?> на обновления Ваших историй!
<?php include('_footer.php') ?>