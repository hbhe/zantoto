<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsProductOption' => $modelsProductOption,
        'modelsProductOptionValue' => $modelsProductOptionValue,
    ]) ?>

</div>
