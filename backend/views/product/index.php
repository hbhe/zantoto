<?php

use common\models\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

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
    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
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
            // ['class' => 'yii\grid\CheckboxColumn'],
            [
                'attribute' => 'member_id',
                'visible' => YII_ENV_DEV,
            ],
            [
                'attribute' => 'id',
                'headerOptions' => array('style' => 'width:60px;'),
            ],

            [
                //'attribute' => 'main_image_thumb',
                'attribute' => 'mainThumbImageUrl',
                'format' => ['image', ['width'=>'32', 'height'=>'32']],
            ],
            //'title',
            [
                'attribute' => 'shortTitle',
                'headerOptions' => array('style' => 'width:200px;'),
            ],

//            [
//                'attribute' => 'category_path',
//                'visible' => YII_ENV_DEV,
//            ],

//            'category_id1',
//            'category_id2',
//            'category_id3',

            [
                'attribute' => 'categoryPathName',
                'headerOptions' => array('style' => 'width:160px;'),
            ],
            'spu_id',
            // 'sub_title',
            // 'brand_id',
            //'custom_brand',

            'quantity',
             'price',
            // 'cost_price',
            // 'market_price',
//            'sold_volume',
//            'award_revenue',
//            'award_fish',
//            'award_coupon',
            // 'accept_coupon_ratio',
            // 'shipping',
            // 'has_option',
            // 'sort_order',
            // 'detail:ntext',
            // 'memo',
            // 'total_rate_score',
            [
                'attribute' => 'total_rate_score',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    $str = '';
                    $str .= Html::a($model->total_rate_score, ['/rate/index', 'is_platform' => 1, 'product_id' => $model->id]);
                    return $str;
                },
            ],

            // 'status',
//            'status_listing',
//
//            'statusListingString',
            [
                'attribute' => 'status_listing',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    $str = $model->statusListingString;
                    if ($model->status_listing == Product::STATUS_LISTING_ON) {
                        $str .= "<br/>" . $model->listing_time;
                    }
                    if ($model->status_listing == Product::STATUS_LISTING_OFF) {
                        $str .= "<br/>" . $model->delisting_time;
                    }
                    return $str;
                },
            ],
            // 'created_at',
            // 'updated_at',
//             'listing_time',
//             'delisting_time',
            [
                'label' => ' ',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    $str = '';
                    //$str = Html::a('规格 ', ['/product-option/index', 'product_id' => $model->id]);
                    $str .= Html::a('SKU ', ['/sku/index', 'product_id' => $model->id]);
                    return $str;
                },
                'visible' => false,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                // 'template' => '{update} {view} {delete}',
                'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{update} {view}',
                //'options' => ['style'=>'width: 100px;'],
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url . "&is_platform={$model->is_platform}", [
                            //'class' => 'btn btn-xs btn-primary',
                            'title' => Yii::t('plugin', 'Update'),
                        ]);
                    },
                    'view' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url . "&is_platform={$model->is_platform}", [
                            //'class' => 'btn btn-xs btn-warning',
                            'title' => Yii::t('plugin', 'View'),
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url . "&is_platform={$model->is_platform}", [
                            //'class' => 'btn btn-xs btn-danger',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('plugin', 'Are you sure to delete this item?'),
                            'title' => Yii::t('plugin', 'Delete'),
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],

        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>