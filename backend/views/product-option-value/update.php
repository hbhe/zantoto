<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductOptionValue */

$this->title = '更新：' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Product Option Values', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="product-option-value-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
