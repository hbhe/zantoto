<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OptionValue */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Option Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="option-value-view">

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
            'option_id',
            'name',
            'image',
            'sort_order',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

