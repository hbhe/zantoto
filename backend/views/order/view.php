<?php

use common\models\Order;
use common\wosotech\base\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => $model->is_platform ? '平台订单' : '商户订单', 'url' => ['index', 'is_platform' => $model->is_platform]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

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
            [
                'attribute' => 'id',
                'captionOptions' => ['width' => '20%'],
            ],
            'tid',
            'is_platform:boolean',
            'member_id',
            'buyer_id',
            'mobile',
            'nickname',
            'total_amount',
            'quantity',
//            'coupon_used',
            'shipping_fee',
            'pay_amount',
            'pay_method',
            'pay_time',
//            'coupon_award',
//            'fish_award',
//            'revenue_status',
//            'revenue_amount',
//            'is_giftkey:boolean',
            'shipping_address',
            'parentAreaCodeName',
            'areaCodeName',
            'districtAreaCodeName',
            'shipping_zipcode',
            'shipping_name',
            'shipping_mobile',
            'shipping_time',
            'express_company',
            'express_code',
            'statusString',
            //'has_refund',
            'created_at',
            'updated_at',
            'rate_time',
            'memo',
        ],
    ]) ?>


    <?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['id' => 'grid_id', 'class' => 'table-responsive'],
    'layout' => "{items}\n",
    //'filterModel' => $searchModel,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],
        //['class' => 'yii\grid\CheckboxColumn'],
        'id',
        //'order_id',
        //'member_id',
        'product_id',
        [
            'attribute' => 'title',
            'headerOptions' => array('style' => 'width:160px;'),
        ],
        //'buyer_id',
        'sku_code',
        //'main_image',
        [
            //'attribute' => 'main_image_thumb',
            'attribute' => 'thumbImageUrl',
            'label' => '图片',
            'format' => ['image', ['width'=>'32','height'=>'32']],
        ],
        'option_value_names',
        'price',
        // 'image',
        'quantity',
        'amount',
//        'coupon_used',
        //'sku.acceptCouponAmount',
        [
            'attribute' => 'sku.acceptCouponAmount',
            'visible' => false, //YII_ENV_DEV,
        ],
//        'is_rated:boolean',
        [
            //'label' => '是否评价',
            'attribute' => 'is_rated',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                return $model->is_rated == 0 ? '未评' : ('已评' . Html::a(ArrayHelper::getValue($model, 'rate.score'), ['rate/view', 'id' => ArrayHelper::getValue($model, 'rate.id')]));
            },
        ],

        'statusString',
        // 'created_at',
        // 'updated_at',
    ],
]); ?>

</div>

