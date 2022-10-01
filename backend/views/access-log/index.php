<?php

use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AccessLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '操作日志';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="access-log-index">

        <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?php //echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php $form = ActiveForm::begin([
            'fieldConfig' => ['enableLabel' => false],
            'enableClientScript' => false,
        ]); ?>

        <?php $this->beginBlock('panel'); ?>
        <div class="grid-panel form-group row">
            <div class="col-md-12">
                <?php echo Html::submitInput('删除', ['name' => 'delete', 'class' => 'btn btn-primary',]) // 'data' => ['method'=> 'post', 'confirm' => '确认删除?'] ?>
            </div>

            <?php
            $js = <<<EOD
                $(".grid-panel .btn").click(function() {
                    var ids = $('#grid_id').yiiGridView('getSelectedRows');
                    if (ids.length == 0) {
                        alert("请至少勾选一条记录!");
                        return false;
                    }
                    return true;
                });
EOD;
            $this->registerJs($js, yii\web\View::POS_READY);
            ?>
        </div>
        <?php $this->endBlock(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}\n<div>{$this->blocks['panel']}</div>\n{pager}",
            'options' => ['id' => 'grid_id'], //'class' => 'table-responsive'
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],

                [
                    'attribute' => 'id',
                    'headerOptions' => array('style'=>'width:50px;'),
                ],
                [
                    'attribute' => 'user_id',
                    'label' => '操作者ID',
                    'headerOptions' => array('style'=>'width:80px;'),
                ],

                [
                    'attribute' => 'user.username',
                    'value' => function ($model, $key, $index, $column) {
                        return ArrayHelper::getValue($model, 'user.username');
                    },
                    'headerOptions' => array('style'=>'width:120px;'),
                ],

                'ip',
                [
                    'attribute' => 'detail',
                    'format' => 'raw',
                    'headerOptions' => array('style'=>'width:400px;'),
                ],

                'created_at',
                // 'updated_at',

                [
                    'class' => 'yii\grid\ActionColumn',
                    //'template' => '{update} {view} {delete}',
                    'template' => '{delete}',
                    'visible' => false,
                ]
            ],
        ]); ?>

        <?php ActiveForm::end(); ?>

    </div>


