<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Area Codes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="area-code-index">


    <p>
        <?php echo Html::a('Create Area Code', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'name',
            'parent_id',
            'zip',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
