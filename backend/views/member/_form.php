<?php

use common\models\AreaCode;
use common\models\Member;
use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Member */
/* @var $profile common\models\MemberProfile */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="member-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->isNewRecord || YII_DEBUG): ?>
        <?php echo $form->field($model, 'pid')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <?php // echo $form->field($model, 'id')->textInput() ?>

    <?php // echo $form->field($model, 'sid')->textInput(['maxlength' => true]) ?>

    <?php // echo $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'area_parent_id')->dropDownList(AreaCode::getProvinceOption(), ['prompt' => '选择...', 'id' => 'parent_id']); ?>

    <?php echo Html::hiddenInput('selected_id', $model->isNewRecord ? '' : $model->area_id, ['id' => 'selected_id']); ?>
    <?php echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options' => ['id' => 'area_id', 'class' => '', 'style' => ''],
        'pluginOptions' => [
            'depends' => ['parent_id'],
            'placeholder' => '选择...',
            'initialize' => $model->isNewRecord ? false : true,
            'url' => Url::to(['/area-code/subcat']),
            'params' => ['selected_id']
        ],
    ]); ?>

<!--    --><?php //echo $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'access_token')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'password_plain')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'status')->dropDownList(Member::getStatusArray()) ?>

    <?php echo $form->field($model, 'is_seller')->dropDownList(Util::getYesNoOptionName()) ?>

    <?php echo $form->field($model, 'balance_revenue')->textInput(['maxlength' => true]) ?>
    
<!--    --><?php //echo $form->field($model, 'gender')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'area_parent_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'area_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'age')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'avatar_path')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'avatar_base_url')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'logged_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'pid')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'status_bind')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'status_audit')->textInput() ?>
<!---->


    <?php echo $form->field($model, 'picture')->widget(\trntv\filekit\widget\Upload::classname(), [
        'url' => ['avatar-upload'], // 如果不指定fileparam, 会将自动生成的name赋给fileparam，增加到url中一起发过去
        //'url'=>['avatar-upload', 'fileparam' => 'file_avatar'],
        //'multiple' => true,
        //'maxNumberOfFiles' => 4
        //'sortable' => true,
        //'maxFileSize' => 10 * 1024 * 1024, // 10 MiB
        //'clientOptions' => [ ...other blueimp options... ]
    ]) ?>

    <!--    --><?php //echo $form->field($model, 'openid')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($profile, 'is_real_name')->dropDownList(Util::getYesNoOptionName()) ?>

    <?php echo $form->field($profile, 'identity')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($profile, 'card_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($profile, 'card_name')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($profile, 'card_branch')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($profile, 'card_bank')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($profile, 'alipay_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($profile, 'alipay_name')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
