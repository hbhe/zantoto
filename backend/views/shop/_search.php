<?php

use common\models\Seller;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SellerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="seller-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group row">
        <div class="col-md-4">
            <?= $form->field($model, 'member_id') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'title') ?>
        </div>

        <div class="col-md-4">
            <?php echo $form->field($model, 'seller_status')->dropDownList(Seller::getSellerStatusArray(), ['prompt' => '']) ?>

<!--            --><?//= $form->field($model, 'status')->dropDownList(common\wosotech\helper\Util::getYesNoOptionName(), ['prompt' => '']) ?>
        </div>

    </div>
<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'member_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'cat') ?>
<!---->
<!--    --><?//= $form->field($model, 'title') ?>
<!---->
<!--    --><?//= $form->field($model, 'company') ?>

    <?php // echo $form->field($model, 'credit_code') ?>

    <?php // echo $form->field($model, 'area_parent_id') ?>

    <?php // echo $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'district_id') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'open_time') ?>

    <?php // echo $form->field($model, 'tel') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'legal_person') ?>

    <?php // echo $form->field($model, 'legal_identity') ?>

    <?php // echo $form->field($model, 'business_licence_image') ?>

    <?php // echo $form->field($model, 'identity_face_image') ?>

    <?php // echo $form->field($model, 'identity_back_image') ?>

    <?php // echo $form->field($model, 'logo') ?>

    <?php // echo $form->field($model, 'seller_status') ?>

    <?php // echo $form->field($model, 'seller_time') ?>

    <?php // echo $form->field($model, 'seller_reason') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

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


