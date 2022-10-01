<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = '更新'; // . $model->title;
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="product-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsProductOption' => $modelsProductOption,
        'modelsProductOptionValue' => $modelsProductOptionValue,
    ]) ?>

</div>
