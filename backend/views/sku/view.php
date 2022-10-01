<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Sku */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Skus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sku-view">

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
            'sku_code',
            'member_id',
            'product_id',
            'option_value_ids',
            'option_value_names',
            'query_string',
            'option_value_names_md5',
            'quantity',
            'price',
            'sold_volume',
            'image',
            'sort_order',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

