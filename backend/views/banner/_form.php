<?php

use common\models\Banner;
use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Banner */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php
$jump_type = Html::getInputId($model,'jump_type');
$js = <<<EOD
$("#$jump_type").change( function() {   
    var jump_type = $("#$jump_type").val();
    $("#url_div, #app_div").hide();
    if (jump_type == '1') {
        $("#url_div").show();
    }
    if (jump_type == '3') {
        $("#app_div").show();
    }
}).change();

EOD;
$this->registerJs($js, yii\web\View::POS_READY);
?>

<div class="banner-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'cat')->textInput() ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'detail')->textarea(['rows' => 6]) ?>

<!--    --><?php //echo $form->field($model, 'img_id')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'img_url')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'img_id')->widget(\noam148\imagemanager\components\ImageManagerInputWidget::className(), [
        'cropViewMode' => 1,
        'aspectRatio' => (1080/2160),
        'showPreview' => true,
        'showDeletePickedImageConfirm' => false, //on true show warning before detach image
    ]); ?>

    <?php echo $form->field($model, 'jump_type')->dropDownList(Banner::getJumpTypeArray()) ?>

    <div id="url_div">
        <?php echo $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
    </div>

    <div id="app_div">
        <?php echo $form->field($model, 'app_function_id')->dropDownList(Banner::getAppFunctionArray()) ?>
    </div>

    <?php // echo $form->field($model, 'second')->textInput() ?>

    <?php echo $form->field($model, 'sort_order')->textInput() ?>

    <?php echo $form->field($model, 'status')->dropDownList(Util::getYesNoOptionName()) ?>

<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

