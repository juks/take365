<?php
use yii\helpers\Html;

use app\assets\AppAsset;
use app\assets\FrontAsset;
use app\models\Feed;

AppAsset::register($this);

$user = Yii::$app->user;
$isSubscribed = !$user->isGuest && Feed::isSubscribed($user);
$this->registerJs('smartdate.init({locale: \'ru\'});');
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="ru-RU"
<?php if ($this->params['pageType'] == 'story'): ?> class="page-story"<?php endif ?>
<?php if ($this->params['pageType'] == 'home'): ?> class="page-stories"<?php endif ?>>
<head>
  <title><?= Html::encode($this->title) ?></title>
  <meta charset="UTF-8">
  <meta name="viewport" content="initial-scale=1, viewport-fit=cover">
  <meta name="keywords" content="365 фотографий, 365 дней, проект 365 дней, год фотографий, фото-год, по одной фотографии на каждый день, идеи фотографий на каждый день, take365">
  <meta name="color-scheme" content="light dark">
  <meta name="theme-color" media="(prefers-color-scheme: light)" content="#fff">
  <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#141414">
  <link rel="stylesheet" href="/css/light.css" media="(prefers-color-scheme: light)">
  <link rel="stylesheet" href="/css/dark.css" media="(prefers-color-scheme: dark)">
  <link rel="icon" href="<?= \yii\helpers\Url::base(true) ?>/i/favicon.ico">
  <?php $this->head(); echo "\n"; ?>
  <script><?= $this->params['jsVarsString'] ?></script>
</head>
<body>
<?php $this->beginBody() ?>
  <header class="main-header">
    <a href="<?= \yii\helpers\Url::base(true) ?>">
      <div class="main-header-logo">take365</div>
    </a>
  </header>
  <nav class="main-nav">
    <ul class="main-nav-list">
      <?php if ($user->isGuest) echo $this->render('//blocks/userMenu/anonymous'); else echo $this->render('//blocks/userMenu/logged', ['user' => $user->identity, 'owner' => isset($this->params['owner']) ? $this->params['owner'] : null, 'isSubscribed' => $isSubscribed]); ?>
    </ul>
  </nav>
  <?php echo $this->render('//blocks/userMenu/searchBlock'); ?>
  <?php
    $this->registerJs("searchRender(document.getElementById('search-container'));");
  ?>
  <?= $content ?>
  <?php include('includes/footer.php'); ?>
  <?php include('includes/loginForm.php'); ?>
  <?php include('includes/ga.php'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
