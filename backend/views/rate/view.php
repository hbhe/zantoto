<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Rate */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '商品评论', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="rate-view">

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
                'member_id',
                'product_id',
                'product_title',
                'buyer_id',
                'nickname',
                'score',
                'content',

                'ip',
                'is_anonymous:boolean',
                'is_hidden:boolean',
                'order_id',
                [
                    'attribute' => 'order_sku_id',
                    'value' => ArrayHelper::getValue($model, 'orderSku.option_value_names'),
                ],
                [
                    // 正式采用blueimp gallery样式, 纯图片
                    'attribute' => 'rate_pictures',
                    'format' => 'raw',
                    'value' => function ($model, $widget) {
                        $items = [];
                        $str = '';
                        foreach ($model->pictures as $picture) {
                            if ($picture->isImage()) {
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
                            'templateOptions' => ['id' => 'rate_pictures'],
                            'clientOptions' => ['container' => '#rate_pictures'],
                        ]); // . implode(',', $model->picturesDownloadUrls);
                    }
                ],

                //'status',
                'sort_order',
                'created_at',
                'updated_at',
            ],
        ]) ?>

    </div>
