<div>Ololo posts list</div>

<?php if ($posts): ?>
    <?php foreach($posts as $post): ?>
        <?= $this->render('//blocks/blogPost', ['post' => $post]); ?>
    <?php endforeach ?>
<?php endif ?>
