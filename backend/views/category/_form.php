<?php

use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'parent_id')->textInput() ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>

    <?php echo Html::a(Html::img($model->getThumbUploadUrl('icon', 'thumb'), ['class' => 'img-responsive']), $model->getUploadUrl('icon') , ["target" => "_blank"]) // preview ?>
    <?php echo $form->field($model, 'icon')->fileInput(['accept' => 'image/*', 'name' => 'icon']) // 这里使用name标签，因为behavior用了instanceByName = true ?>

<!--    --><?php //echo $form->field($model, 'path')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'depth')->textInput() ?>

    <?php echo $form->field($model, 'keyword')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <?php echo $form->field($model, 'is_visual')->dropDownList(Util::getYesNoOptionName()) ?>

    <?php echo $form->field($model, 'sort_order')->textInput() ?>


<!--    --><?php //echo $form->field($model, 'status')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>
<!---->
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

