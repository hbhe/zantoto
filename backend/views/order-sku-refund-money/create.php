<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OrderSkuRefund */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '退货列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-sku-refund-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
