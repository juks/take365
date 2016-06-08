<?php include('_header.php') ?>
<p>Пользователь <a href="<?= $reader->url ?>"><?= $reader->fullnameFilled ?></a> <?= $this->hUserAction($reader, 'подписался', 'подписалась') ?> на обновления Ваших историй!</p>
<?php include('_footer.php') ?>