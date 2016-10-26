<?php if ($mediaList): ?>
    <?php foreach($mediaList as $item): ?>
        <div>
            <img src="<?= $item['media']['t']['maxSide'][700]['url'] ?>" width="<?= $item['media']['t']['maxSide'][700]['width'] ?>" height="<?= $item['media']['t']['maxSide'][700]['height'] ?>">
        </div>
    <?php endforeach ?>
<?php else: ?>
    <div>Ololo nothing found</div>
<?php endif ?>
