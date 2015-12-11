<div class="story-matrix"><?php if ($story->images): ?><?php foreach ($story->images as $image): ?>
  <a href="<?= $story->url ?>" class="story-matrix-item"><?php if (empty($image['isEmpty'])): ?><img src="<?= $image['t']['squareCrop']['100']['url'] ?>" alt=""><?php else: ?><div class="user-photo-empty-holder50">&nbsp;</div><?php endif ?></a><?php endforeach ?></a><?php endif ?>
</div>