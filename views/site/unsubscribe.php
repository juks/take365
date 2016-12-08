<main class="content">
  <section class="content-info">
    <header class="content-header">
			<h1 class="content-title">Настройка уведомлений</h1>
    </header>
    <?php if ($actionResult): ?>
		  <p><?= $actionResult ?></p>
		<?php endif;?>
		<p>
		  <a href="<?= $actionUrl ?>"><?= $actionTitle ?></a>
		</p>
  </section>
</main>
