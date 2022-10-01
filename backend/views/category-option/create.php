<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CategoryOption */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => 'Category Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-option-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
