<?php

use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MemberSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="member-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php /* 
    <div class="form-group row">
        <div class="col-md-4">
            <?= $form->field($model, 'id') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'status')->dropDownList(common\wosotech\helper\Util::getYesNoOptionName(), ['prompt' => '']) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'created_at')->widget('\kartik\daterange\DateRangePicker', [
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
    */ ?>

    <div class="form-group row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>
            <?= $form->field($model, 'nickname') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'mobile') ?>
<!--            --><?php //echo $form->field($model, 'is_star')->dropDownList(Util::getYesNoOptionName(), ['prompt' => '']) ?>

        </div>
        <div class="col-md-3">
<!--            --><?php //echo $form->field($model, 'is_outlet')->dropDownList(Util::getYesNoOptionName(), ['prompt' => '']) ?>
            <?php echo $form->field($model, 'is_seller')->dropDownList(Util::getYesNoOptionName(), ['prompt' => '']) ?>
        </div>

        <div class="col-md-3">
            <?php // echo $form->field($model, 'status')->dropDownList(Member::getStatusArray(), ['prompt' => '']) ?>
<!--            --><?php //echo $form->field($model, 'is_real_name')->dropDownList(Util::getYesNoOptionName(), ['prompt' => '']) ?>
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
<!--    --><?//= $form->field($model, 'sid') ?>
<!---->
<!--    --><?//= $form->field($model, 'username') ?>
<!---->
<!--    --><?//= $form->field($model, 'mobile') ?>
<!---->
<!--    --><?//= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'nickname') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'access_token') ?>

    <?php // echo $form->field($model, 'password_plain') ?>

    <?php // echo $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'gender') ?>

    <?php // echo $form->field($model, 'area_parent_id') ?>

    <?php // echo $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'age') ?>

    <?php // echo $form->field($model, 'avatar_path') ?>

    <?php // echo $form->field($model, 'avatar_base_url') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'logged_at') ?>

    <?php // echo $form->field($model, 'pid') ?>

    <?php // echo $form->field($model, 'status_bind') ?>

    <?php // echo $form->field($model, 'status_audit') ?>

    <?php // echo $form->field($model, 'openid') ?>

    <?php // echo $form->field($model, 'identity') ?>

    <?php // echo $form->field($model, 'weixin_number') ?>

    <?php // echo $form->field($model, 'card_id') ?>

    <?php // echo $form->field($model, 'card_name') ?>

    <?php // echo $form->field($model, 'card_branch') ?>

    <?php // echo $form->field($model, 'card_bank') ?>

    <?php // echo $form->field($model, 'alipay_id') ?>

    <?php // echo $form->field($model, 'alipay_name') ?>

    <?php // echo $form->field($model, 'balance_revenue') ?>

    <?php // echo $form->field($model, 'balance_power') ?>

    <?php // echo $form->field($model, 'balance_fish') ?>

    <?php // echo $form->field($model, 'balance_coupon') ?>

    <?php // echo $form->field($model, 'is_real_name') ?>

    <?php // echo $form->field($model, 'is_star') ?>

    <?php // echo $form->field($model, 'is_seller') ?>

    <?php // echo $form->field($model, 'is_outlet') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


