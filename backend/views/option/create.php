<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Option */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '商品规格', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="option-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'parent' => $parent,
        'sons' => $sons
    ]) ?>

</div>
