<a href="/panel/newsletter-write">Создать</a>

<?php if ($newsletters): ?>
    <?php foreach($newsletters as $newsletter): ?>
        <?= $this->render('//blocks/newsletter', ['newsletter' => $newsletter]); ?>
    <?php endforeach ?>
<?php else: ?>
    <div>No newsletters</div>
<?php endif ?>
