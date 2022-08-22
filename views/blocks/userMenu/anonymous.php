<li class="main-nav-list-item">
  <a href="#" onclick="Auth.open(event,this);return false">Вход</a>
</li>
<li class="main-nav-list-item">
  <a href="<?= \yii\helpers\Url::base(true) ?>/register">Регистрация</a>
</li>
<?php include('searchBlock.php'); ?>
<?php
$this->registerJs("searchRender(document.getElementById('search-container'));");
?>
