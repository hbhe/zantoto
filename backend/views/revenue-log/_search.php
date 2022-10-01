<?php

use common\models\RevenueLog;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RevenueLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="revenue-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'member_id') ?>
        </div>

        <div class="col-md-3">
<!--            --><?//= $form->field($model, 'status')->dropDownList(common\wosotech\helper\Util::getYesNoOptionName(), ['prompt' => '']) ?>
            <?= $form->field($model, 'kind')->dropDownList(RevenueLog::getKindArray(), ['prompt' => '']) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'title') ?>
        </div>


    </div>

<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'member_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'kind') ?>
<!---->
<!--    --><?//= $form->field($model, 'title') ?>
<!---->
<!--    --><?//= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'memo') ?>

    <?php // echo $form->field($model, 'order_amount') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'order_time') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


