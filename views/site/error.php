<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>

<main class="main">
  <section class="content">
    <header class="content-header">
      <h1 class="content-title"><?= Html::encode($this->title) ?></h1>
    </header>
    <p class="text"><?= nl2br(Html::encode($message)) ?></p>
    <p class="text">The above error occurred while the Web server was processing your request.</p>
    <p class="text">Please contact us if you think this is a server error. Thank you.</p>
  </section>
</main>
