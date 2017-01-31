<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $post app\models\Post */
/* @var $form ActiveForm */

?>

<a href="/panel/blog" class="blog-all-link"><i class="fa fa-long-arrow-up" aria-hidden="true"></i> Все записи</a>
<div class="blog-post">
  <?php
    $form = ActiveForm::begin(
      [
        'options' => [
          'id'        => 'postForm',
          'name'      => 'postForm',
          'class'     => 'form'
        ]
      ]
    );
    $post->blog_id = 1;
  ?>

  <?= $form->field($post, 'id')->hiddenInput()->label(false) ?>
  <?= $form->field($post, 'blog_id')->hiddenInput()->label(false) ?>
  <?= $form->field($post, 'title')->label('Заголовок') ?>
  <?= $form->field($post, 'body')->label('Текст')->textArea(['rows' => '10']) ?>
  <?= $form->field($post, 'is_published')->checkbox(['value' => 1, 'label' => 'Запись опубликована']) ?>

  <fieldset>
    <?= Html::submitInput(Yii::t('app', 'Опубликовать')) ?>
  </fieldset>
  <?php ActiveForm::end(); ?>
</div>
