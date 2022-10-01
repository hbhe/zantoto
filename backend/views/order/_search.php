<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => Url::current(),
        'method' => 'get',
    ]); ?>

    <div class="form-group row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'buyer_id') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'mobile') ?>
        </div>

        <div class="col-md-3">
            <?php echo $form->field($model, 'created_at')->widget('\kartik\daterange\DateRangePicker', [
                'presetDropdown' => true,
                'defaultPresetValueOptions' => ['style'=>'display:none'],
                'options' => [
                    'id' => 'created_at'
                ],
                'pluginOptions' => [
                    'format' => 'YYYY-MM-DD',
                    'separator' => ' TO ',
                    'opens'=>'left',
                ] ,
                'pluginEvents' => [
                    //"apply.daterangepicker" => "function() { $('.grid-view').yiiGridView('applyFilter'); }",
                ]
            ]) ?>

        </div>

    </div>


<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'tid') ?>
<!---->
<!--    --><?//= $form->field($model, 'member_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'buyer_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'nickname') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'coupon_used') ?>

    <?php // echo $form->field($model, 'shipping_fee') ?>

    <?php // echo $form->field($model, 'pay_amount') ?>

    <?php // echo $form->field($model, 'pay_method') ?>

    <?php // echo $form->field($model, 'pay_time') ?>

    <?php // echo $form->field($model, 'coupon_award') ?>

    <?php // echo $form->field($model, 'fish_award') ?>

    <?php // echo $form->field($model, 'revenue_status') ?>

    <?php // echo $form->field($model, 'revenue_amount') ?>

    <?php // echo $form->field($model, 'revenue_before') ?>

    <?php // echo $form->field($model, 'revenue_after') ?>

    <?php // echo $form->field($model, 'shipping_address') ?>

    <?php // echo $form->field($model, 'shipping_zipcode') ?>

    <?php // echo $form->field($model, 'shipping_name') ?>

    <?php // echo $form->field($model, 'shipping_mobile') ?>

    <?php // echo $form->field($model, 'shipping_time') ?>

    <?php // echo $form->field($model, 'express_company') ?>

    <?php // echo $form->field($model, 'express_code') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'has_refund') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'memo') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>

