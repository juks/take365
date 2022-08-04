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
  <title><?= Html::encode($this->title) ?> — по одной фотографии на каждый день</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="initial-scale=1, viewport-fit=cover">
  <meta name="keywords" content="365 фотографий, 365 дней, проект 365 дней, год фотографий, фото-год, по одной фотографии на каждый день, идеи фотографий на каждый день, take365">
  <meta name="color-scheme" content="light dark">
  <meta name="theme-color" media="(prefers-color-scheme: light)" content="#fff">
  <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#141414">
  <?php $this->head() ?>
  <script>
    <?= $this->params['jsVarsString'] ?>;
    $(function() {
      Bg.create(pp.ids, pp.urls, pp.maxSpritesPerFile, pp.currentMosaicId);
    });
  </script>
  <link rel="stylesheet" href="/css/light.css" media="(prefers-color-scheme: light)">
  <link rel="stylesheet" href="/css/dark.css" media="(prefers-color-scheme: dark)">
</head>
<body>
<?php $this->beginBody() ?>
  <?= $content ?>
  <?php include('includes/footer.php'); ?>
  <?php include('includes/loginForm.php'); ?>
  <?php include('includes/ga.php'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
