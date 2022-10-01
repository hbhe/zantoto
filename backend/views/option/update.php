<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Option */

$this->title = '更新：' . $parent->name;
$this->params['breadcrumbs'][] = ['label' => '商品规格', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="option-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'parent' => $parent,
        'sons' => $sons
    ]) ?>

</div>
