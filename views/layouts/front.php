<?php

use yii\helpers\Html;

use app\assets\AppAsset;
use app\assets\FrontAsset;

AppAsset::register($this);
FrontAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<!-- fix one page scroll v.1.3.1 for ie9 -->
<!--[if lte IE 9]><html class="ie8"><![endif]-->
<!--[if gt IE 9]><!--><html lang="ru-RU"><!--<![endif]-->
<head>
  <title><?= Html::encode($this->title) ?></title>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <?php $this->head() ?>
  <script>
    <?= $this->params['jsVarsString'] ?>

    window.REGISTRATION_INDEX = 2;
    $(function(){
      $('.content').onepage_scroll({
        sectionContainer: 'section',
        responsiveFallback: 600,
        animationTime: 600,
        easing: 'ease-in-out',
        pagination: false,
        beforeMove: function(index) {
          // more fps on scroll
          document.body.style.pointerEvents = 'none';

          $('.header-nav-register').toggleClass('active', index === REGISTRATION_INDEX);
          // fix не срабатывает afterMove когда мы перемещаемся на индекс на котором находимся. v1.3.1
          setTimeout(function() {
            document.body.style.pointerEvents = 'auto';
          }, 600);
        },
        afterMove: function(index) {
          document.body.style.pointerEvents = 'auto';
        },
        loop: false
      });

      $('.header-nav-register').toggleClass('active', location.hash === '#' + REGISTRATION_INDEX);

      Bg.create(pp.ids, pp.urls, pp.maxSpritesPerFile, pp.currentMosaicId);
    });
  </script>
</head>
<body class="main">
<?php $this->beginBody() ?>
  <?= $content ?>
  <?php include('includes/loginForm.php'); ?>
  <?php include('includes/ga.php'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

