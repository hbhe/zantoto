<?php

use common\models\Category;
use common\models\Product;
use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="product-form">

<!--    --><?php
//    $cat = Html::getInputId($model,'accept_coupon_cat');
//    $catName = Html::getInputName($model,'accept_coupon_cat');
//    $js = <<<EOD
//
//$("#$cat").change( function() {
//    var cat = $('input:radio[name="$catName"]:checked').val();
//    if (cat == '0') {
//        $("#ratio").show();
//        $("#amount").hide();
//    } else {
//        $("#ratio").hide();
//        $("#amount").show();
//    }
//}).change();
//
//EOD;
//    $this->registerJs($js, yii\web\View::POS_READY);
//    ?>

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

<!--    --><?php //echo $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?php if(YII_DEBUG): ?>
    <?php echo $form->field($model, 'member_id')->textInput() ?>
    <?php endif; ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'sub_title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'brand_id')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'category_id')->textInput() ?>

    <?php echo $form->field($model, 'category_id1')->dropDownList(Category::getFirstCategoryOption(), ['prompt' => '选择...', 'id' => 'category_id1']); ?>

    <?php echo Html::hiddenInput('selected_category_id2', $model->isNewRecord ? '' : $model->category_id2, ['id' => 'selected_category_id2']); ?>
    <?php echo $form->field($model, 'category_id2')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options' => ['id' => 'category_id2', 'class' => '', 'style' => ''],
        'pluginOptions' => [
            'depends' => ['category_id1'],
            'placeholder' => '选择...',
            'initialize' => $model->isNewRecord ? false : true,
            'url' => Url::to(['/category/subcat1']),
            'params' => ['selected_category_id2']
        ],
    ]); ?>

    <?php echo Html::hiddenInput('selected_category_id3', $model->isNewRecord ? '' : $model->category_id3, ['id' => 'selected_category_id3']); ?>
    <?php echo $form->field($model, 'category_id3')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options' => ['id' => 'category_id3', 'class' => '', 'style' => ''],
        'pluginOptions' => [
            'depends' => ['category_id1', 'category_id2'],
            'placeholder' => '选择...',
            'initialize' => $model->isNewRecord ? false : true,
            'url' => Url::to(['/category/subcat2']),
            'params' => ['selected_category_id3']
        ],
    ]); ?>


<!--    --><?php //echo $form->field($model, 'category_path')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'spu_id')->textInput(['maxlength' => true]) ?>


<!--    --><?php //echo $form->field($model, 'sub_title')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'brand_id')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'custom_brand')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'main_image')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'quantity')->textInput() ?>

    <?php echo $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'sold_volume')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'award_revenue')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'award_fish')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'award_coupon')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'accept_coupon_cat')->inline()->radioList(Product::getAcceptCouponCatArray())->label('接收礼券设置'); ?>
<!---->
<!--    <div id="ratio">-->
<!--    --><?php //echo $form->field($model, 'accept_coupon_ratio')->textInput(['maxlength' => true])->hint('输入范围(0~1), 如0.34表示34%') ?>
<!--    </div>-->
<!---->
<!--    <div id="amount">-->
<!--    --><?php //echo $form->field($model, 'accept_coupon_amount')->textInput(['maxlength' => true]) ?>
<!--    </div>-->
    <!--    --><?php //echo $form->field($model, 'shipping')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'sort_order')->textInput() ?>

    <?php echo $form->field($model, 'status_listing')->dropDownList(Product::getStatusListingArray()) ?>

<!--    --><?php //echo $form->field($model, 'is_star_product')->dropDownList(Util::getNoYesOptionName()) ?>

<!--    --><?php //echo $form->field($model, 'detail')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'memo')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'total_rate_score')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'status')->dropDownList(Util::getYesNoOptionName()) ?>

<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'listing_time')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'delisting_time')->textInput() ?>

    <?php echo $form->field($model, 'product_pictures')->widget(\trntv\filekit\widget\Upload::classname(), [
        'url' => ['product-pictures-upload'],
        'multiple' => true,
        'maxNumberOfFiles' => 9,
        'sortable' => true,
        'maxFileSize' => 12 * 1024 * 1024, // 8 MiB
        //'clientOptions' => [ ...other blueimp options... ]
    ]) ?>

    <?php echo $form->field($model, 'detail_pictures')->widget(\trntv\filekit\widget\Upload::classname(), [
        'url' => ['detail-pictures-upload'],
        'multiple' => true,
        'maxNumberOfFiles' => 6,
        'sortable' => true,
        'maxFileSize' => 12 * 1024 * 1024, // 8 MiB
        //'clientOptions' => [ ...other blueimp options... ]
    ]) ?>

    <hr />

    <?php echo $form->field($model, 'has_option')->dropDownList(Util::getYesNoOptionName())->hint('对规格的增删或者规格取值的增删都会先清除已存在的SKU数据, 请谨慎操作!') ?>

    <?php if (!$model->isNewRecord): //if (Yii::$app->request->get('need_edit_option')): ?>

    <div id="id_div_form_son">
    <?= $this->render('_form_son', [
        'form' => $form,
        'model' => $model,
        'modelsProductOption' => $modelsProductOption,
        'modelsProductOptionValue' => $modelsProductOptionValue,
    ]) ?>
    </div>

        <?php
        $cat = Html::getInputId($model,'has_option');
        $catName = Html::getInputName($model,'has_option');

        $js = <<<EOD
$("#$cat").change( function() { 
    var cat = $("#$cat").val();
    if (cat == '1') {
        $("#id_div_form_son").show();
    } else {
        $("#id_div_form_son").hide();    
    }
}).change();

EOD;
        $this->registerJs($js, yii\web\View::POS_READY);
        ?>

    <?php endif; ?>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

