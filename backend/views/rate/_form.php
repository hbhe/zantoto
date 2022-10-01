<?php

use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Rate */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="rate-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'member_id')->textInput() ?>
<!---->
        <?php if ($model->isNewRecord): ?>

        <?php echo $form->field($model, 'buyer_id')->textInput() ?>

        <?php echo $form->field($model, 'product_id')->textInput(['maxlength' => true]) ?>

            <!--    --><?php //echo $form->field($model, 'product_title')->textInput(['maxlength' => true]) ?>
        <?php endif; ?>

    <?php echo $form->field($model, 'content')->textarea(['maxlength' => true, 'rows' => 2]) ?>

    <?php echo $form->field($model, 'score')->inline()->radioList(['1' => 1, '2' => 2, '3' => 3]) ?>

<!--    --><?php //echo $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'is_anonymous')->dropDownList(Util::getNoYesOptionName()) ?>

    <?php echo $form->field($model, 'is_hidden')->dropDownList(Util::getNoYesOptionName()) ?>

<!--    --><?php //echo $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'status')->textInput() ?>
    <?php echo $form->field($model, 'rate_pictures')->widget(\trntv\filekit\widget\Upload::classname(), [
        'url' => ['rate-pictures-upload'],
        'multiple' => true,
        'maxNumberOfFiles' => 9,
        'sortable' => true,
        'maxFileSize' => 12 * 1024 * 1024, // 8 MiB
        //'clientOptions' => [ ...other blueimp options... ]
    ]) ?>

    <?php echo $form->field($model, 'sort_order')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
