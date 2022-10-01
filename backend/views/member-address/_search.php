<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MemberAddressSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="member-address-search">

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

    <?= $form->field($model, 'member_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'mobile') ?>

    <?= $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'is_default') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


