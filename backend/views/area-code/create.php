<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AreaCode */

$this->title = 'Create Area Code';
$this->params['breadcrumbs'][] = ['label' => 'Area Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="area-code-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
