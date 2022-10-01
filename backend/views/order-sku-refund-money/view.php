<?php

use common\models\OrderSkuRefund;
use common\wosotech\base\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OrderSkuRefund */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '退款', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-sku-refund-view">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <p style="display:none;">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'product_id',

            [
            'attribute' => 'orderSku.thumbImageUrl',
            'format' => ['image', ['width' => '100', 'height' => '100']],
            //'visible' => YII_ENV_DEV,
             ],

//        [
//            'attribute' => 'orderSku.imageUrl',
//            'format' => ['image',], // ['width' => '100', 'height' => '100']
//        ],

//            [
//                'attribute' => 'product.imageUrl',
//                'format' => 'raw',
//                'value' => Html::a(Html::img(ArrayHelper::getValue($model, 'orderSku.thumbImageUrl'), ['width' => 100]), ArrayHelper::getValue($model, 'orderSku.imageUrl'), ["target" => "_blank"]),
//            ],

            //'member_id',
            'buyer_id',
            'mobile',
            'nickname',
            'order_sku_id',
            'need_ship:boolean',
            'refund_reason',
            'refund_detail',
            'refund_amount',
            'refund_coupon',
            'handled_by',
            'handled_time',
            'handled_memo',
//            'shipping_address',
//            //'shipping_zipcode',
//            'shipping_name',
//            'shipping_mobile',
//            'shipping_by',
//            'shipping_time',
//            'shipping_memo',
//            'express_company',
//            'express_code',
            'statusString',
            'created_at',

//            'updated_at',
//            'image1',
//            'image2',
//            'image3',
            [
                'attribute' => 'image1Url',
                'format' => 'raw',
                'value' => Html::a(Html::img($model->image1ThumbUrl, ['width' => 100]), $model->image1Url, ["target" => "_blank"]),
            ],
            [
                'attribute' => 'image2Url',
                'format' => 'raw',
                'value' => Html::a(Html::img($model->image2ThumbUrl, ['width' => 100]), $model->image2Url, ["target" => "_blank"]),
            ],
            [
                'attribute' => 'image3Url',
                'format' => 'raw',
                'value' => Html::a(Html::img($model->image3ThumbUrl, ['width' => 100]), $model->image3Url, ["target" => "_blank"]),
            ],

        ],
    ]) ?>

</div>
<hr>

<?php if ($model->status == OrderSkuRefund::STATUS_WAIT): ?>
<?php $form = ActiveForm::begin(); ?>
    <!--    --><?php //echo $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'product_id')->textInput(['maxlength' => true]) ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'member_id')->textInput() ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'buyer_id')->textInput() ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'order_sku_id')->textInput() ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'need_ship')->textInput() ?>

<?php //echo $form->field($model, 'refund_reason')->textInput(['maxlength' => true]) ?>

<?php // echo $form->field($model, 'refund_detail')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'refund_amount')->textInput(['maxlength' => true])->label('确认退款金额') ?>
<!---->
<?php //echo $form->field($model, 'refund_coupon')->textInput(['maxlength' => true])->label('确认退款礼券') ?>

<?php //echo $form->field($model, 'status')->dropDownList(OrderSkuRefund::getStatusOptions(), ['prompt' => '']) ?>

    <!--    --><?php //echo $form->field($model, 'handled_by')->textInput() ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'handled_time')->textInput() ?>

<?php echo $form->field($model, 'handled_memo')->textarea(['maxlength' => true, 'rows' => 2]) ?>

<?php //echo $form->field($model, 'shipping_address')->textInput(['maxlength' => true]) ?>

<?php // echo $form->field($model, 'shipping_zipcode')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'shipping_name')->textInput(['maxlength' => true]) ?>
<!---->
<?php //echo $form->field($model, 'shipping_mobile')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'shipping_by')->textInput() ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'shipping_time')->textInput() ?>

<?php //echo $form->field($model, 'shipping_memo')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'express_company')->textInput(['maxlength' => true]) ?>
<!---->
<?php //echo $form->field($model, 'express_code')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>

    <!--    --><?php //echo $form->field($model, 'image1')->textInput(['maxlength' => true]) ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'image2')->textInput(['maxlength' => true]) ?>
    <!---->
    <!--    --><?php //echo $form->field($model, 'image3')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?php //echo Html::submitButton('更新', ['class' => 'btn btn-warning', 'data-confirm' => '确认退款?']) ?>
        <?php echo Html::submitInput('确认退款', ['name' => 'confirm', 'class' => 'btn btn-danger', 'data-confirm' => '确认退款?']) ?>
        <?php echo Html::submitInput('拒绝退款', ['name' => 'refuse', 'class' => 'btn btn-primary', 'data-confirm' => '拒绝退款?']) ?>
    </div>

<?php ActiveForm::end(); ?>
<?php endif; ?>

