<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RevenueLog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="revenue-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'member_id')->textInput() ?>

    <?php echo $form->field($model, 'kind')->textInput() ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'memo')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'order_amount')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'created_at')->textInput() ?>

    <?php echo $form->field($model, 'updated_at')->textInput() ?>

    <?php echo $form->field($model, 'order_time')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

