<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Seller */

$this->title = '更新：' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Sellers', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="seller-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
