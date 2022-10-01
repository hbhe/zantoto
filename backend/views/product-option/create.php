<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProductOption */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => 'Product Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-option-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        //'model' => $model,
        'modelCatalogOption' => $modelCatalogOption,
        'modelsOptionValue' => $modelsOptionValue, //(empty($modelsOptionValue)) ? [new OptionValue] : $modelsOptionValue

    ]) ?>

</div>
