<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Cart */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '购物车', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
