<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RevenueLog */

$this->title = '更新：' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Revenue Logs', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="revenue-log-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
