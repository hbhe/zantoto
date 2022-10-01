<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrderSkuRefundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-sku-refund-search">

    <?php $form = ActiveForm::begin([
        'action' => Url::current(),
        'method' => 'get',
    ]); ?>

    <div class="form-group row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>
<!--            --><?//= $form->field($model, 'order_id') ?>
        </div>
        <div class="col-md-3">
<!--            --><?//= $form->field($model, 'buyer_id') ?>
            <?php echo $form->field($model, 'nickname') ?>
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
<!--    --><?//= $form->field($model, 'order_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'product_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'member_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'buyer_id') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'nickname') ?>

    <?php // echo $form->field($model, 'order_sku_id') ?>

    <?php // echo $form->field($model, 'need_ship') ?>

    <?php // echo $form->field($model, 'refund_reason') ?>

    <?php // echo $form->field($model, 'refund_detail') ?>

    <?php // echo $form->field($model, 'refund_amount') ?>

    <?php // echo $form->field($model, 'refund_coupon') ?>

    <?php // echo $form->field($model, 'handled_by') ?>

    <?php // echo $form->field($model, 'handled_time') ?>

    <?php // echo $form->field($model, 'handled_memo') ?>

    <?php // echo $form->field($model, 'shipping_address') ?>

    <?php // echo $form->field($model, 'shipping_zipcode') ?>

    <?php // echo $form->field($model, 'shipping_name') ?>

    <?php // echo $form->field($model, 'shipping_mobile') ?>

    <?php // echo $form->field($model, 'shipping_by') ?>

    <?php // echo $form->field($model, 'shipping_time') ?>

    <?php // echo $form->field($model, 'shipping_memo') ?>

    <?php // echo $form->field($model, 'express_company') ?>

    <?php // echo $form->field($model, 'express_code') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'image1') ?>

    <?php // echo $form->field($model, 'image2') ?>

    <?php // echo $form->field($model, 'image3') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


