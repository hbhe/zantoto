<?php

use common\models\Order;
use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $searchModel->is_platform ? '平台订单' : '商户订单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <?php /* 
    <?= \yii\bootstrap\Nav::widget([
        'options' => [
            'class' => 'nav nav-tabs',
            'style' => 'margin-bottom: 15px'
        ],
        'items' => [
            [
                'label'   => 'Tabs-1',
                'url'     => ['index', 'cat' => 1],
                'active' => Yii::$app->request->get('cat') == 1,
            ],
            [
                'label'   => 'Tabs-2',
                'url'     => ['index', 'cat' => 2],
                'active' => true,
            ],
        ]
    ]) ?>
    */
 ?>

<!--    <h1 style="display:none;">--><?//= Html::encode($this->title) ?><!--</h1>-->

    <?= \yii\bootstrap\Nav::widget([
        'options' => [
            'class' => 'nav nav-tabs',
            'style' => 'margin-bottom: 15px'
        ],
        'items' => [
            [
                'label' => '全部',
                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform],
                'active' => Yii::$app->request->get('status') === null,
            ],
            [
                'label' => Order::getStatusOptions()[Order::STATUS_AUCTION],
                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform, 'status' => Order::STATUS_AUCTION],
                'active' => Yii::$app->request->get('status') == Order::STATUS_AUCTION,
            ],
            [
                'label' => Order::getStatusOptions()[Order::STATUS_PAID],
                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform, 'status' => Order::STATUS_PAID],
                'active' => Yii::$app->request->get('status') == Order::STATUS_PAID,
            ],
            [
                'label' => Order::getStatusOptions()[Order::STATUS_SHIPPED],
                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform, 'status' => Order::STATUS_SHIPPED],
                'active' => Yii::$app->request->get('status') == Order::STATUS_SHIPPED,
            ],
            [
                'label' => Order::getStatusOptions()[Order::STATUS_CONFIRM],
                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform, 'status' => Order::STATUS_CONFIRM],
                'active' => Yii::$app->request->get('status') == Order::STATUS_CONFIRM,
            ],
            [
                'label' => Order::getStatusOptions()[Order::STATUS_RATED],
                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform, 'status' => Order::STATUS_RATED],
                'active' => Yii::$app->request->get('status') == Order::STATUS_RATED,
            ],
            [
                'label' => Order::getStatusOptions()[Order::STATUS_CLOSED],
                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform, 'status' => Order::STATUS_CLOSED],
                'active' => Yii::$app->request->get('status') == Order::STATUS_CLOSED,
            ],
//            [
//                'label' => 'GIFTKEY',
//                'url' => ['/order/index', 'is_platform' => $searchModel->is_platform, 'status' => Order::STATUS_GIFT_KEY_WAIT],
//                'active' => Yii::$app->request->get('status') == Order::STATUS_GIFT_KEY_WAIT,
//            ],
        ]
    ])
    ?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (YII_ENV_DEV): ?>
    <p>
        <?php echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif; ?>

    <?php $form = common\wosotech\base\ActiveForm::begin(['fieldConfig' => [
        'enableLabel' => false,
    ]
    ]); ?>

    <?php $this->beginBlock('panel'); ?>
    <div class="form-group row">
        <div class="col-md-3">
            <?php echo $form->field($searchModel, 'id')->dropDownList([1 => 'YES', '0' => 'NO']) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($searchModel, 'id')->textInput(['placeholder' => 'ID']) ?>
        </div>
        <div class="col-md-3">
            <?php echo Html::submitInput('Begin', ['name' => 'begin', 'class' => 'btn btn-primary', 'data' => ['confirm' => '确认?']]) ?>
        </div>

        <?php         $js = <<<EOD
        $(".grid-panel .btn").click(function() {
            var ids = $('#grid_id').yiiGridView('getSelectedRows');
            if (ids.length == 0) {
                alert("请至少勾选一条记录!");
                return false;                
            }
            return true;
        });
EOD;
        $this->registerJs($js, yii\web\View::POS_READY);
        ?>
    </div>
    <?php $this->endBlock(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'grid_id', 'class' => 'table-responsive'],
        'layout' => "{summary}\n{items}\n<div class='grid-panel hide'>{$this->blocks['panel']}</div>\n{pager}",
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'], 
            //['class' => 'yii\grid\CheckboxColumn'],
                        'id',
            [
                'attribute' => 'tid',
                'visible' => YII_ENV_DEV,
            ],
            [
                'attribute' => 'member_id',
                //'visible' => YII_ENV_DEV,
            ],
            'buyer_id',
            'mobile',
            // 'nickname',
             'total_amount',
             'pay_amount',
             'quantity',
            // 'coupon_used',
            // 'shipping_fee',
            // 'pay_amount',
            // 'coupon_award',
            // 'fish_award',
            // 'revenue_status',
            // 'revenue_amount',
            // 'revenue_before',
            // 'revenue_after',
            // 'shipping_address',
            // 'shipping_zipcode',
            // 'shipping_name',
            // 'shipping_mobile',
            // 'shipping_time',
            // 'express_company',
            // 'express_code',
            //'pay_time',
            //'pay_method',
            [
                'attribute' => 'pay_time',
            ],

            [
                'attribute' => 'shipping_time',
                'visible' => Yii::$app->request->get('status') == Order::STATUS_SHIPPED,
            ],
            [
                'attribute' => 'confirm_time',
                'visible' => Yii::$app->request->get('status') == Order::STATUS_CONFIRM,
            ],
            [
                'attribute' => 'rate_time',
                'visible' => Yii::$app->request->get('status') == Order::STATUS_RATED,
            ],
             //'statusString',
             'statusForSellerString',
            // 'has_refund',
            //'is_giftkey',
             'created_at',
            // 'updated_at',
            // 'memo',

            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{update} {view} {delete}',
                //'template' => '{update} {delete}',
                'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{update} {view}',

            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>


