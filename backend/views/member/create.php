<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'profile' => $profile,
    ]) ?>

</div>
