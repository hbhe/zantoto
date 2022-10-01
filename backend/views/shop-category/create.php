<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OutletCategory */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '联盟门店分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outlet-category-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
