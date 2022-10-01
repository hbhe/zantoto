<?php

use common\models\OrderSkuRefund;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrderSkuRefund */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="order-sku-refund-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'product_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'member_id')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'buyer_id')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'order_sku_id')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'need_ship')->textInput() ?>

    <?php echo $form->field($model, 'refund_reason')->textInput(['maxlength' => true]) ?>

    <?php // echo $form->field($model, 'refund_detail')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'refund_amount')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'refund_coupon')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'status')->dropDownList(OrderSkuRefund::getStatusOptions()) // ['prompt' => ''] ?>

    <!--    --><?php //echo $form->field($model, 'handled_by')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'handled_time')->textInput() ?>

    <?php echo $form->field($model, 'handled_memo')->textarea(['maxlength' => true, 'rows' => 2]) ?>

    <?php echo $form->field($model, 'shipping_address')->textInput(['maxlength' => true]) ?>

    <?php // echo $form->field($model, 'shipping_zipcode')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'shipping_name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'shipping_mobile')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_by')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'shipping_time')->textInput() ?>

    <?php echo $form->field($model, 'shipping_memo')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'express_company')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'express_code')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'image1')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'image2')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'image3')->textInput(['maxlength' => true]) ?>

    <?php echo Html::a(Html::img($model->image1ThumbUrl, ['class' => 'img-responsive']), $model->image1Url , ["target" => "_blank"]) // preview ?>
    <?php echo $form->field($model, 'image1')->fileInput(['accept' => 'image/*', 'name' => 'image1']) // 这里使用name标签，因为behavior用了instanceByName = true ?>

    <?php echo Html::a(Html::img($model->image2ThumbUrl, ['class' => 'img-responsive']), $model->image2Url , ["target" => "_blank"]) // preview ?>
    <?php echo $form->field($model, 'image2')->fileInput(['accept' => 'image/*', 'name' => 'image2']) // 这里使用name标签，因为behavior用了instanceByName = true ?>

    <?php echo Html::a(Html::img($model->image3ThumbUrl, ['class' => 'img-responsive']), $model->image3Url , ["target" => "_blank"]) // preview ?>
    <?php echo $form->field($model, 'image3')->fileInput(['accept' => 'image/*', 'name' => 'image3']) // 这里使用name标签，因为behavior用了instanceByName = true ?>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

