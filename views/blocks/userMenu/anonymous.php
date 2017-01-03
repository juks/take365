<li class="nav-item"><a href="#" onclick="Auth.open(event,this);return false" class="nav-link">Вход</a></li>
<li class="nav-item"><a href="<?= \yii\helpers\Url::base(true) ?>/register" class="nav-link nav-link-register">Регистрация</a></li>
<?php include('searchBlock.php'); ?>
<?php
$this->registerJs("searchRender(document.getElementById('search-container'));");
?>
