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
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="icon" href="{{ $base_full_url }}i/{{ $rev_img }}/favicon.ico">
  <?php $this->head() ?>
  <script>
    <?= $this->params['jsVarsString'] ?>
  </script>
</head>
<body>
<?php $this->beginBody() ?>
  <div class="page">
    <div class="page-wrapper">
      <header class="header">
        <a href="<?= \yii\helpers\Url::base(true) ?>"><div class="header-logo">take365</div></a>
        <ul class="header-nav">
          {{ IF $user }}{{ inc('blocks/userMenu/logged.tpl') }}{{ ELSE }}{{ inc('blocks/userMenu/anonymous.tpl') }}{{ END }}
        </ul>
      </header>
      <div class="content">
        <article class="article">
        	<?= $content ?>
        </article>
      </div>
    </div>
  </div>
  <footer class="footer">
    <ul>
      <li>&copy; 2011&mdash;<?= date("Y") ?> <?= Yii::$app->params['projectTitle'] ?></li>
      <li><a href="/help/">О&nbsp;проекте</a></li>
      <li>
        <a href="http://take365.reformal.ru" onclick="Reformal.widgetOpen();return false;" onmouseover="Reformal.widgetPreload();">Отзывы и&nbsp;предложения</a>
        <script>
          var reformalOptions = { project_id: 66526, show_tab: false, project_host: "take365.reformal.ru" };
          (function() {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.async = true;
            script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
            document.getElementsByTagName('head')[0].appendChild(script); })
          ();
        </script>
      </li>
      <li><a href="mailto:bang@take365.org">bang@take365.org</a></li>
    </ul>
  </footer>
  {{ inc('popups/main.tpl') }}
  <script>{{ $jsInit }}</script>
  {{ inc('blocks/stats/ga.tpl') }}
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

