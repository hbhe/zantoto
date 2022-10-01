<?php

use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderSkuRefundSkuRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '退款列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-sku-refund-index">

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
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\bootstrap\Nav::widget([
        'options' => [
            'class' => 'nav nav-tabs',
            'style' => 'margin-bottom: 15px'
        ],
        'items' => [
            [
                'label' => '全部',
                'url' => ['index',],
                'active' => Yii::$app->request->get('status') === null,
            ],
            [
                'label' => '待处理',
                'url' => ['index', 'status' => common\models\OrderSkuRefund::STATUS_WAIT],
                'active' => Yii::$app->request->get('status') == common\models\OrderSkuRefund::STATUS_WAIT,
            ],
            [
                'label' => '已完成',
                'url' => ['index', 'status' => common\models\OrderSkuRefund::STATUS_CONFIRM],
                'active' => Yii::$app->request->get('status') == common\models\OrderSkuRefund::STATUS_CONFIRM,
            ],
            [
                'label' => '已拒绝',
                'url' => ['index', 'status' => common\models\OrderSkuRefund::STATUS_REFUSED],
                'active' => Yii::$app->request->get('status') == common\models\OrderSkuRefund::STATUS_REFUSED,
            ],
        ]
    ])
    ?>


    <p>
        <?php // echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
            'order_id',
            'product_id',
            //'member_id',
            'buyer_id',
             'mobile',
             'nickname',
            //'order_sku_id',

            // 'need_ship',
            // 'refund_reason',
            // 'refund_detail',
             'refund_amount',
             'refund_coupon',
            // 'handled_by',
            // 'handled_time',
            // 'handled_memo',
            // 'shipping_address',
            // 'shipping_zipcode',
            // 'shipping_name',
            // 'shipping_mobile',
            // 'shipping_by',
            // 'shipping_time',
            // 'shipping_memo',
            // 'express_company',
            // 'express_code',
            //'status',
            'statusString',
            // 'created_at',
            // 'updated_at',
            // 'image1',
            // 'image2',
            // 'image3',

            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{view} {delete}',
                'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{update} {view}',
                //'template' => '{update} {delete}',
            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>

