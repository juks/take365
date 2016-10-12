<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $form ActiveForm */
?>
<div class="Post">

    <?php
        $form = ActiveForm::begin();
        $model->blog_id = 1;
        $model->is_published = 1;
    ?>

        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'blog_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'is_published')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'title') ?>
        <?= $form->field($model, 'body')->textArea(['rows' => '6']) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- Post -->