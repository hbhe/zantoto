<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Picture */

$this->title = '更新'; // . $model->name;
$this->params['breadcrumbs'][] = ['label' => '图片', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="picture-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
