<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductOption */

$this->title = '更新：' . $modelCatalogOption->id;
$this->params['breadcrumbs'][] = ['label' => 'Product Options', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="product-option-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        //'model' => $model,
        'modelCatalogOption' => $modelCatalogOption,
        'modelsOptionValue' => $modelsOptionValue, //(empty($modelsOptionValue)) ? [new ProductOptionValue] : $modelsOptionValue

    ]) ?>

</div>
