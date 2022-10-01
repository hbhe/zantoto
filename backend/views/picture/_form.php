<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Picture */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="picture-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'global_iid')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'global_sid')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'path')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'base_url')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'size')->textInput() ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'order')->textInput() ?>

    <?php echo $form->field($model, 'created_at')->textInput() ?>

    <?php echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

