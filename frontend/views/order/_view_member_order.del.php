<?php

use common\wosotech\base\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Member */

//$this->title = $model->id;
//$this->params['breadcrumbs'][] = ['label' => '用户收单', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;

?>
<?php //Pjax::begin(); ?>
<br/>
<div class="member-view">
    <?= GridView::widget([
        'dataProvider' => $memberOrderDataProvider,
        // 'options' => ['class' => 'table-responsive'],
        // 'layout' => "{summary}\n{items}\n<div class='grid-panel hide'>{$this->blocks['panel']}</div>\n{pager}",
        //'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            // ['class' => 'yii\grid\CheckboxColumn'],
            //'id',
            'order_id',
            [
                'attribute' => 'order.title',
                'headerOptions' => array('style' => 'width:200px;'),
            ],
            'member_id',
            'member.name',
            'statusString',
            'created_at',
            // 'updated_at',
            // 'memo',
            // 'attachment',
            // 'params:ntext',

            [
                'label' => ' ',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    $str = Html::a('查看订单', ['/order/view', 'id' => $model->order_id], ['data' => ['']]);
                    return $str;
                },
                'headerOptions' => array('style' => 'width:100px;'),
                'visible' => false,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'member-order',
                //'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{view} {update}',
                'template' => '{view}',
                'visible' => false,
            ]
        ],
    ]); ?>

</div>
<?php //Pjax::end(); ?>



