<?php

use common\models\AreaCode;
use common\models\OutletCategory;
use common\models\Seller;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Seller */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="seller-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'member_id')->textInput() ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'cat')->dropDownList(Seller::getCatArray()) //['prompt' => ''] ?>

    <?php echo $form->field($model, 'parent_cat')->dropDownList(ArrayHelper::map(OutletCategory::findAll(['parent_id' => 0]), 'id', 'name'), ['prompt' => '选择...', 'id' => 'parent_shop_category_id']); ?>

    <?php echo Html::hiddenInput('selected_shop_category_id', empty($model->cat) ? '' : $model->cat, ['id' => 'selected_shop_category_id']); ?>
    <?php echo $form->field($model, 'cat')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options' => ['id' => 'shop_category_id', 'class' => '', 'style' => ''],
        'pluginOptions' => [
            'depends' => ['parent_shop_category_id'],
            'placeholder' => '选择...',
            'initialize' => true, // $model->isNewRecord ? false : true,
            'url' => Url::to(['/outlet-category/subcat']),
            'params' => ['selected_shop_category_id']
        ],
    ]); ?>


    <?php echo $form->field($model, 'company')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'credit_code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'area_parent_id')->dropDownList(AreaCode::getProvinceOption(), ['prompt' => '选择...', 'id' => 'parent_id']); ?>

    <?php echo Html::hiddenInput('selected_id', empty($model->area_id) ? '' : $model->area_id, ['id' => 'selected_id']); ?>
    <?php echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options' => ['id' => 'area_id', 'class' => '', 'style' => ''],
        'pluginOptions' => [
            'depends' => ['parent_id'],
            'placeholder' => '选择...',
            'initialize' => true, // $model->isNewRecord ? false : true,
            'url' => Url::to(['/area-code/subcat']),
            'params' => ['selected_id']
        ],
    ]); ?>

    <?php echo Html::hiddenInput('selected_district_id', empty($model->district_id) ? '' : $model->district_id, ['id' => 'selected_district_id']); ?>
    <?php echo $form->field($model, 'district_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options' => ['id' => 'district_id', 'class' => '', 'style' => ''],
        'pluginOptions' => [
            'depends' => ['parent_id', 'area_id'],
            'placeholder' => '选择...',
            'initialize' => true, // $model->isNewRecord ? false : true,
            'url' => Url::to(['/area-code/district-subcat']),
            'params' => ['selected_district_id']
        ],
    ]); ?>
    
    <?php echo $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'open_time')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'detail')->textarea(['rows' => 4]) ?>

    <?php echo $form->field($model, 'legal_person')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'legal_identity')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'business_licence_image')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'identity_face_image')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'identity_back_image')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo Html::a(Html::img($model->getThumbUploadUrl('logo', 'thumb'), ['class' => 'img-responsive']), $model->getUploadUrl('logo') , ["target" => "_blank"]) // preview ?>
<!--    --><?php //echo $form->field($model, 'logo')->fileInput(['accept' => 'image/*', 'name' => 'logo']) // 这里使用name标签，因为behavior用了instanceByName = true ?>

    <?php echo $form->field($model, 'logo_picture')->widget(\trntv\filekit\widget\Upload::classname(), [
        'url' => ['logo-upload'], // 如果不指定fileparam, 会将自动生成的name赋给fileparam，增加到url中一起发过去
        //'url'=>['avatar-upload', 'fileparam' => 'file_avatar'],
        //'multiple' => true,
        //'maxNumberOfFiles' => 4
        //'sortable' => true,
        //'maxFileSize' => 10 * 1024 * 1024, // 10 MiB
        //'clientOptions' => [ ...other blueimp options... ]
    ]) ?>

    <?php echo Html::a(Html::img($model->getThumbUploadUrl('business_licence_image', 'thumb'), ['class' => 'img-responsive']), $model->getUploadUrl('business_licence_image') , ["target" => "_blank"]) // preview ?>
    <?php echo $form->field($model, 'business_licence_image')->fileInput(['accept' => 'image/*', 'name' => 'business_licence_image']) // 这里使用name标签，因为behavior用了instanceByName = true ?>

    <?php echo Html::a(Html::img($model->getThumbUploadUrl('identity_face_image', 'thumb'), ['class' => 'img-responsive']), $model->getUploadUrl('identity_face_image') , ["target" => "_blank"]) // preview ?>
    <?php echo $form->field($model, 'identity_face_image')->fileInput(['accept' => 'image/*', 'name' => 'identity_face_image']) // 这里使用name标签，因为behavior用了instanceByName = true ?>

    <?php echo Html::a(Html::img($model->getThumbUploadUrl('identity_back_image', 'thumb'), ['class' => 'img-responsive']), $model->getUploadUrl('identity_back_image') , ["target" => "_blank"]) // preview ?>
    <?php echo $form->field($model, 'identity_back_image')->fileInput(['accept' => 'image/*', 'name' => 'identity_back_image']) // 这里使用name标签，因为behavior用了instanceByName = true ?>

<!--    --><?php //echo $form->field($model, 'seller_time')->textInput() ?>

    <?php echo $form->field($model, 'seller_status')->dropDownList(Seller::getSellerStatusArray()) ?>

    <?php echo $form->field($model, 'seller_reason')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'sort_order')->textInput() ?>

    <?php echo $form->field($model, 'status')->dropDownList(Seller::getStatusArray()) ?>

<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

