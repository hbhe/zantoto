<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PictureSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="picture-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'global_sid') ?>

    <?= $form->field($model, 'path') ?>

    <?= $form->field($model, 'base_url') ?>

    <?= $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'size') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
