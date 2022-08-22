<main class="main">
  <section class="content">
    <header class="content-header">
			<h1 class="content-title">Настройка уведомлений</h1>
    </header>
    <?php if ($actionResult): ?>
		  <p class="text"><?= $actionResult ?></p>
		<?php endif;?>
		<p class="text">
		  <a href="<?= $actionUrl ?>"><?= $actionTitle ?></a>
		</p>
  </section>
</main>
