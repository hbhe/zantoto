<?php

use common\models\Category;
use common\models\Product;
use common\wosotech\helper\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'action' => Url::current(),
        'method' => 'get',
    ]); ?>

    <div class="form-group row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>
            <?php echo $form->field($model, 'category_id1')->dropDownList(Category::getFirstCategoryOption(), ['prompt' => '选择...', 'id' => 'category_id1']); ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'status_listing')->dropDownList(Product::getStatusListingArray(),['prompt' => '']) ?>
<!--            --><?php //echo Html::hiddenInput('selected_category_id2', $model->isNewRecord ? '' : $model->category_id2, ['id' => 'selected_category_id2']); ?>
            <?php echo Html::hiddenInput('selected_category_id2', empty($model->category_id2) ? '' : $model->category_id2, ['id' => 'selected_category_id2']); ?>
            <?php echo $form->field($model, 'category_id2')->widget(\kartik\depdrop\DepDrop::classname(), [
                'options' => ['id' => 'category_id2', 'class' => '', 'style' => ''],
                'pluginOptions' => [
                    'depends' => ['category_id1'],
                    'placeholder' => '选择...',
                    //'initialize' => $model->isNewRecord ? false : true,
                    'initialize' => true,
                    'url' => Url::to(['/category/subcat1']),
                    'params' => ['selected_category_id2']
                ],
            ]); ?>

        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'spu_id') ?>
            <?php // echo Html::hiddenInput('selected_category_id3', $model->isNewRecord ? '' : $model->category_id3, ['id' => 'selected_category_id3']); ?>
            <?php echo Html::hiddenInput('selected_category_id3', empty($model->category_id3) ? '' : $model->category_id3, ['id' => 'selected_category_id3']); ?>
            <?php echo $form->field($model, 'category_id3')->widget(\kartik\depdrop\DepDrop::classname(), [
                'options' => ['id' => 'category_id3', 'class' => '', 'style' => ''],
                'pluginOptions' => [
                    'depends' => ['category_id1', 'category_id2'],
                    'placeholder' => '选择...',
                    //'initialize' => $model->isNewRecord ? false : true,
                    'initialize' => true,
                    'url' => Url::to(['/category/subcat2']),
                    'params' => ['selected_category_id3']
                ],
            ]); ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'title') ?>
<!--            --><?php //echo $form->field($model, 'is_star_product')->dropDownList(Util::getNoYesOptionName(),['prompt' => '']) ?>


        </div>

    </div>
<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'member_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'category_id') ?>
<!---->
<!--    --><?//= $form->field($model, 'category_path') ?>
<!---->
<!--    --><?//= $form->field($model, 'spu_id') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'sub_title') ?>

    <?php // echo $form->field($model, 'brand_id') ?>

    <?php // echo $form->field($model, 'custom_brand') ?>

    <?php // echo $form->field($model, 'main_image') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'cost_price') ?>

    <?php // echo $form->field($model, 'market_price') ?>

    <?php // echo $form->field($model, 'sold_volume') ?>

    <?php // echo $form->field($model, 'award_revenue') ?>

    <?php // echo $form->field($model, 'award_fish') ?>

    <?php // echo $form->field($model, 'award_coupon') ?>

    <?php // echo $form->field($model, 'accept_coupon_ratio') ?>

    <?php // echo $form->field($model, 'shipping') ?>

    <?php // echo $form->field($model, 'has_option') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

    <?php // echo $form->field($model, 'status_listing') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'memo') ?>

    <?php // echo $form->field($model, 'total_rate_score') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'listing_time') ?>

    <?php // echo $form->field($model, 'delisting_time') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default hide']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>


