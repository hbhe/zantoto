<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BannerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="banner-search">

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
    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cat') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'detail') ?>

    <?= $form->field($model, 'img_id') ?>

    <?php // echo $form->field($model, 'img_url') ?>

    <?php // echo $form->field($model, 'jump_type') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'app_function_id') ?>

    <?php // echo $form->field($model, 'second') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


