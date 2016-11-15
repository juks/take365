<?php if ($posts): ?>
    <?php foreach($posts as $post): ?>
        <?= $this->render('//blocks/blogPost', ['post' => $post]); ?>
    <?php endforeach ?>
<?php else: ?>

<?php endif ?>
