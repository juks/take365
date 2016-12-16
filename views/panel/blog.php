<?php

\app\assets\PanelBlogAsset::register($this);

?>
<p><a href="/panel/post-write">Создать запись</a></p>
<?php if ($posts): ?>
    <?php foreach($posts as $post): ?>
        <?= $this->render('//blocks/blogPost', ['post' => $post]); ?>
    <?php endforeach ?>
<?php else: ?>
    <div>Ololo no posts</div>
<?php endif ?>
