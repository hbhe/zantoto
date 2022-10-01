<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AreaCode */

$this->title = 'Update Area Code: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Area Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="area-code-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
