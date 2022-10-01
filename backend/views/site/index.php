<?php

/* @var $this yii\web\View */

use common\wosotech\base\ActiveForm;
use common\wosotech\base\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = '综合统计';
?>

<div class="rate-view">

<!--    <h1 style="display:none;">--><?//= Html::encode($this->title) ?><!--</h1>-->
<!---->
<!--    <p style="display:none;">-->
<!--        --><?//= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <div class="order-search">

        <?php $form = ActiveForm::begin([
            'action' => Url::current(),
            'method' => 'get',
        ]); ?>

        <div class="form-group row">
            <div class="col-md-6">
                <?php echo $form->field($model, 'created_at')->widget('\kartik\daterange\DateRangePicker', [
                    'presetDropdown' => true,
                    'defaultPresetValueOptions' => ['style'=>'display:none'],
                    'options' => [
                        'id' => 'created_at',
                        'name' => 'created_at',
                    ],
                    'pluginOptions' => [
                        'format' => 'YYYY-MM-DD',
                        'separator' => ' TO ',
                        'opens'=>'left',
                    ] ,
                    'pluginEvents' => [
                        //"apply.daterangepicker" => "function() { $('.grid-view').yiiGridView('applyFilter'); }",
                    ],
                ])->label(false) ?>

            </div>
            <div class="col-md-6">
                <?= Html::submitButton('统计', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <?= DetailView::widget([
        'model' => $orderData,
        'attributes' => [
            [
                'attribute' => 'total_amount',
                'label' => '销售总额',
                'format' => 'currency',
                'captionOptions' => ['width' => '30%'],
            ],
            'valid_count:decimal:有效订单总数',
            'valid_amount:currency:有效订单总额',
            'invalid_count:decimal:无效订单总数',
            'invalid_amount:currency:无效订单总额',
            'done_count:decimal:已成交订单总数',
            'done_amount:currency:已成交订单总额',
        ],
    ]) ?>

    <br/>
    <?= GridView::widget([
        'dataProvider' => $userDataProvider,
        'options' => ['id' => 'grid_id', 'class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-bordered'], // table-striped
        'layout' => "{items}\n",
        'headerRowOptions' => ['style' => 'background-color:#ddd'],
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'title',
                'label' => '',
                'format' => 'raw',
            ],
            'total:decimal:总人数',
            'today:decimal:今日新增',
            'month:decimal:本月新增',
        ],
    ]); ?>

    <br/>
    <?= GridView::widget([
        'dataProvider' => $userOrderDataProvider,
        'options' => ['id' => 'grid_id', 'class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'layout' => "{items}\n",
        'headerRowOptions' => ['style' => 'background-color:#ddd'],
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'title',
                'label' => '',
                'format' => 'raw',
            ],
            'headcount:decimal:人数',
            'count:decimal:订单总数',
            'amount:currency:购物总额',
            'count_per_member:decimal:每用户订单数',
            'amount_per_member:currency:每用户购物额',
        ],
    ]); ?>


</div>

