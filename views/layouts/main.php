<?php

use yii\helpers\Html;
use app\assets\AppAsset;
use app\assets\FrontAsset;

//Yii::$app->getAssetManager()->getBundle('app')->register($this);
AppAsset::register($this);
//FrontAsset::register($this);

$user = Yii::$app->user;

?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="ru-RU">
<head>
<title><?= Html::encode($this->title) ?></title>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<link rel="icon" href="<?= \yii\helpers\Url::base(true) ?>/i/favicon.ico">
<?php $this->head(); echo "\n"; ?>
<script><?= $this->params['jsVarsString'] ?></script>
</head>
<body>
<?php $this->beginBody() ?>
  <div class="page">
    <div class="page-wrapper">
      <header class="header">
        <a href="<?= \yii\helpers\Url::base(true) ?>"><div class="header-logo">take365</div></a>
        <ul class="header-nav">
          <?php if ($user->isGuest) echo $this->render('//blocks/userMenu/anonymous'); else echo $this->render('//blocks/userMenu/logged', ['user' => $user->identity]); ?>
        </ul>
      </header>
      <div class="content">
        <article class="article">
        	<?= $content ?>
          <div class="cl"></div>
        </article>
      </div>
    </div>
  </div>
  <?php include('includes/footer.php'); ?>
  <?php include('includes/loginForm.php'); ?>
  <?php include('includes/ga.php'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

