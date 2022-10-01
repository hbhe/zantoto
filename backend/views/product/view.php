<?php

use common\wosotech\base\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="product-view">

        <h style="display:none;"><?= Html::encode($this->title) ?></h>

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

        <?php echo Html::a('编辑基础信息和规格', ['/product/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('编辑SKU', ['/sku/index', 'product_id' => $model->id], ['class' => 'btn btn-info']) ?>

        <br/>
        <br/>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'id',
                    'captionOptions' => ['width' => '20%'],
                ],
                'member_id',
                //'main_image_thumb',
                [
                    //'attribute' => 'main_image_thumb',
                    'attribute' => 'mainThumbImageUrl',
                    'format' => ['image', ['width' => '100', 'height' => '100']],
                    'visible' => YII_ENV_DEV,
                ],
                [
                    //'attribute' => 'main_image',
                    'attribute' => 'mainImageUrl',
                    'format' => ['image', ['width' => '100', 'height' => '100']],
                    'visible' => false,
                ],
                'title',
                'sub_title',

//            'category_id1',
//            'category_id2',
//            'category_id3',
                [
                    'attribute' => 'category_path',
                    'visible' => YII_ENV_DEV,
                ],
                'categoryPathName',
                'spu_id',
//            'brand_id',
//            'custom_brand',
//            'main_image',
                'quantity',
                'price',
                'cost_price',
                'market_price',
//                'sold_volume',
//                'award_revenue',
//                'award_fish',
//                'award_coupon',
                //'accept_coupon_cat',
                //'acceptCouponCatString',
//                'acceptCouponCatComboString',
//                'accept_coupon_ratio',
//                'accept_coupon_amount',
//            'shipping',
                // 'sort_order',
                //'status_listing',
                'statusListingString',
//            'detail:ntext',
                'memo',
                'total_rate_score',
//                'is_star_product:boolean',
//            'status',
                'created_at',
                'updated_at',
                'listing_time',
                'delisting_time',

                [
                    // 正式采用blueimp gallery样式, 纯图片
                    'attribute' => 'product_pictures',
                    'format' => 'raw',
                    'value' => function ($model, $widget) {
                        $items = [];
                        $str = '';
                        foreach ($model->productPictures as $picture) {
                            if ($picture->isImage())
                            {
                                $items[] = [
                                    'url' => $picture->imageUrl,
                                    //'src' => $picture->imageUrl, // small
                                    'src' => $picture->thumbImageUrl, // small
                                    'options' => array('title' => ''),
                                    'imageOptions' => ['width' => '100'],
                                ];
                            }
                        };
                        return dosamigos\gallery\Gallery::widget(['items' => $items,
                            'templateOptions' => ['id' => 'product_pictures'],
                            'clientOptions' => ['container' => '#product_pictures'],
                        ]); // . implode(',', $model->picturesDownloadUrls);
                    }
                ],

                [
                    // 正式采用blueimp gallery样式, 纯图片
                    'attribute' => 'detail_pictures',
                    'format' => 'raw',
                    'value' => function ($model, $widget) {
                        $items = [];
                        $str = '';
                        foreach ($model->detailPictures as $picture) {
                            if ($picture->isImage())
                            {
                                $items[] = [
                                    'url' => $picture->imageUrl,
                                    //'src' => $picture->imageUrl, // small
                                    'src' => $picture->thumbImageUrl, // small
                                    'options' => array('title' => ''),
                                    'imageOptions' => ['width' => '100'],
                                ];
                            }
                        };
                        return dosamigos\gallery\Gallery::widget(['items' => $items,
                            'templateOptions' => ['id' => 'detail_pictures'],
                            'clientOptions' => ['container' => '#detail_pictures'],
                        ]); // . implode(',', $model->picturesDownloadUrls);
                    }
                ],

                'has_option:boolean',

            ],
        ]) ?>

    </div>


<?php echo $model->getOptionsTableHtml() ?>

<br />

<?php if ($model->is_platform): ?>
<div>
    <?php echo Html::a('从规格库中重新获取该商品分类的规格', ['/product/init-category-option', 'id' => $model->id], [
        'class' => 'btn btn-danger float-right ',
        'data' => [
            'method' => 'post',
            'confirm' => '建议商品的分类定下来后不要再修改分类也不要增减商品规格!, 修改分类或增减规格需要重新获取该分类的规格库, 这会先清除已经存在的规格数据和SKU数据, 确认继续?',
        ]
    ]) ?>
</div>
<?php endif; ?>


<?php /* echo GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['id' => 'grid_id', 'class' => 'table-responsive'],
    'layout' => "{items}\n",
    //'filterModel' => $searchModel,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],
        //['class' => 'yii\grid\CheckboxColumn'],
        //'id',
        //'member_id',
        //'product_id',
        //'option_id',
        'name',
        'productOptionValuesString',
        //'required',
        // 'default_value',
        // 'status',
        // 'created_at',
        // 'updated_at',

    ],
]); */ ?>


