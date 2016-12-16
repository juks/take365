<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $post app\models\Post */
/* @var $form ActiveForm */

?>

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
  <?= $form->field($post, 'title') ?>
  <?= $form->field($post, 'body')->textArea(['rows' => '10']) ?>
  <?= $form->field($post, 'is_published')->checkbox(['value' => 1, 'label' => 'Запись опубликована']) ?>

    <fieldset>
      <?= Html::submitInput(Yii::t('app', 'Submit')) ?>
    </fieldset>
  <?php ActiveForm::end(); ?>
</div>
