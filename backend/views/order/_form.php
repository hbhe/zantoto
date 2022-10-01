<?php

use common\models\Order;
use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'tid')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'member_id')->textInput(['readonly' => true]) ?>

    <?php echo $form->field($model, 'buyer_id')->textInput(['readonly' => true]) ?>

    <?php echo $form->field($model, 'mobile')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?php echo $form->field($model, 'nickname')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?php echo $form->field($model, 'total_amount')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'quantity')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'coupon_used')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_fee')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'pay_amount')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'pay_method')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'pay_time')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'coupon_award')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'fish_award')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'revenue_status')->dropDownList(Util::getNoYesOptionName()) ?>

<!--    --><?php //echo $form->field($model, 'revenue_amount')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'revenue_before')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'revenue_after')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_address')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'shipping_zipcode')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'shipping_name')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'shipping_mobile')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_time')->textInput() ?>

    <?php echo $form->field($model, 'express_company')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'express_code')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'status')->dropDownList(Order::getStatusOptions()) ?>
    <?php echo $form->field($model, 'status')->dropDownList(Order::getStatusOptionsForSeller()) ?>

<!--    --><?php //echo $form->field($model, 'has_refund')->dropDownList(Order::getRefundStatusOptions()) ?>


<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>
<!---->
    <?php echo $form->field($model, 'memo')->textarea(['rows' => 2]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

