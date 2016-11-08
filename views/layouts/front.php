<?php

use yii\helpers\Html;

use app\assets\AppAsset;
use app\assets\FrontAsset;

AppAsset::register($this);
FrontAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="ru-RU">
<head>
  <title><?= Html::encode($this->title) ?></title>
  <meta charset="utf-8">
  <meta name="keywords" content="365 фотографий, 365 дней, проект 365 дней, год фотографий, фото-год, по одной фотографии на каждый день, идеи фотографий на каждый день, take365">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <?php $this->head() ?>
</head>
<body class="main">
<?php $this->beginBody() ?>
  <?= $content ?>
  <?php include('includes/footer.php'); ?>
  <?php include('includes/loginForm.php'); ?>
  <?php include('includes/ga.php'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

