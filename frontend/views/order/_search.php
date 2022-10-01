<?php

use common\models\AreaCode;
use common\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group row">
        <div class="col-md-4">
            <?= $form->field($model, 'id') ?>
            <?php echo $form->field($model, 'area_parent_id')->dropDownList(AreaCode::getProvinceOption(), ['prompt' => '选择...', 'id' => 'parent_id']); ?>

        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'status')->dropDownList(Order::getStatusOptions(), ['prompt' => '']) ?>
            <?php echo Html::hiddenInput('selected_id', empty($model->area_id) ? '' : $model->area_id, ['id'=>'selected_id']); ?>
            <?php echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                'options' => ['id' => 'area_id', 'class' => '', 'style' => ''],
                'pluginOptions' => [
                    'depends' => ['parent_id'],
                    'placeholder' => '选择...',
                    'initialize' => true, //$model->isNewRecord ? false : true,
                    'url' => Url::to(['/area-code/subcat']),
                    'params'=> ['selected_id']
                ],
                'pluginEvents' => [
                    "depdrop.change"=>"function(event, id, value, count) {
                        // alert('depdrop.change-' + id + '-' + value + '-' + count);
                        // var cat1_name = $('#cat1_name').val();
                     }",
                ],

            ]); ?>

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
<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'order_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'member_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'mobile') ?>
<!---->
<!--    --><?//= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'area_parent_id') ?>

    <?php // echo $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'logo') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'start_date') ?>

    <?php // echo $form->field($model, 'days') ?>

    <?php // echo $form->field($model, 'headcount') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'memo') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


