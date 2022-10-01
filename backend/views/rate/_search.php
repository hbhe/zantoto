<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group row">
        <div class="col-md-4">
            <?= $form->field($model, 'order_id') ?>
            <?php echo $form->field($model, 'content') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'product_id') ?>
            <?php echo $form->field($model, 'score') ?>
        </div>

        <div class="col-md-4">
            <?php echo $form->field($model, 'product_title') ?>
            <?= $form->field($model, 'buyer_id') ?>
        </div>

    </div>

<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'order_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'member_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'buyer_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'product_id') ?>

    <?php // echo $form->field($model, 'product_title') ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'score') ?>

    <?php // echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'is_anonymous') ?>

    <?php // echo $form->field($model, 'nickname') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


