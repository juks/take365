<?php

use yii\helpers\Html;
use app\assets\AppAsset;
use app\assets\FrontAsset;
use app\models\Feed;

//Yii::$app->getAssetManager()->getBundle('app')->register($this);
AppAsset::register($this);
//FrontAsset::register($this);

$user = Yii::$app->user;
$isSubscribed = !$user->isGuest && Feed::isSubscribed($user);

?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="ru-RU"
<?php if ($this->params['pageType'] == 'story'): ?> class="page-story"<?php endif ?>
<?php if ($this->params['pageType'] == 'home'): ?> class="page-stories"<?php endif ?>>
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
        <h1 class="header-logo">
          <a href="<?= \yii\helpers\Url::base(true) ?>">
            take365
            <!-- <sup class="header-logo-text">блог</sup> -->
          </a>
        </h1>
      </header>
      <nav class="nav">
        <ul class="nav-list">
          <?php if ($user->isGuest) echo $this->render('//blocks/userMenu/anonymous'); else echo $this->render('//blocks/userMenu/logged', ['user' => $user->identity, 'owner' => isset($this->params['owner']) ? $this->params['owner'] : null, 'isSubscribed' => $isSubscribed]); ?>
        </ul>
      </nav>
      <?= $content ?>
    </div>
  </div>
  <?php include('includes/footer.php'); ?>
  <?php include('includes/loginForm.php'); ?>
  <?php include('includes/ga.php'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
