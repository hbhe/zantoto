<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProductOptionValue */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Product Option Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-option-value-view">

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
            'product_option_id',
            'member_id',
            'product_id',
            'option_id',
            'option_name',
            'option_value_id',
            'option_value_name',
            'sort_order',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
