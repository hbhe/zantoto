<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OutletCategory */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="outlet-category-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'parent_id')->textInput() ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'sort_order')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

