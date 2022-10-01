<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OrderSku */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Skus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-sku-view">

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
            'member_id',
            'buyer_id',
            'sku_code',
            'main_image',
            'main_image_thumb',
            'option_value_names',
            'price',
            'image',
            'quantity',
            'amount',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

