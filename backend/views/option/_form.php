<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $parent common\models\Option */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="option-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'options' => [
            //'enctype' => 'multipart/form-data',
            'id' => 'dynamic-form'
        ]
    ]); ?>

    <div class="form-group row">
        <div class="col-md-12">
            <?php echo $form->field($parent, 'name')->textInput(['maxlength' => true]) ?>
        </div>

<!--        <div class="col-md-6">-->
<!--            --><?php //echo $form->field($parent, 'sort_order')->textInput() ?>
<!--        </div>-->
    </div>

<!--    --><?php //echo $form->field($parent, 'type')->textInput() ?>

<!--    --><?php //echo $form->field($parent, 'name')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($parent, 'alias')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($parent, 'sort_order')->textInput() ?>

<!--    --><?php //echo $form->field($parent, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($parent, 'updated_at')->textInput() ?>

    <?= $this->render('_form_sons', [
        'form' => $form,
        'parent' => $parent,
        'sons' => $sons
    ]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($parent->isNewRecord ? '创建' : '更新', ['class' => $parent->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
