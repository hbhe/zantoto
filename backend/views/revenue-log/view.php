<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RevenueLog */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Revenue Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revenue-log-view">

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
            'member_id',
            'kind',
            'title',
            'amount',
            'memo',
            'order_amount',
            'created_at',
            'updated_at',
            'order_time',
        ],
    ]) ?>

</div>
