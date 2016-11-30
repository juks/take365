<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $form ActiveForm */

?>

<div class="newsletter">
  <?php
    $form = ActiveForm::begin(
      [
        'options' => [
          'id'    => 'newsletterForm',
          'name'  => 'newsletterForm',
          'class' => 'form'
        ]
      ]
    );
  ?>

    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'title') ?>
    <?= $form->field($model, 'body')->textArea(['rows' => '6']) ?>

    <fieldset>
      <?= Html::submitInput(Yii::t('app', 'Submit')) ?>
      <?php if ($model->id): ?><a href="#" id="newsletterTest">Test</a><?php endif ?>
      <?php if (!$model->time_sent): ?><a href="#" id="newsletterDeliver">Deliver</a><?php endif ?>
    </fieldset>
  <?php ActiveForm::end(); ?>
</div>
