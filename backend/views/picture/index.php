<?php

use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PictureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '图片库';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="picture-index">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'], 

            'id',
            'global_sid',
            'path',
            'base_url:url',
            'type',
             'size',
             'name',
             'order',
             'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => '{update} {view} {delete}',
                // 'template' => '{update} {delete}',
                'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{update} {view}',
            ]
        ],
    ]); ?>
</div>

