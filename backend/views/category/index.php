<?php

use common\models\Category;
use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '上级分类:' . $model->parentsNodePath;
//$this->params['breadcrumbs'][] = $this->title;
//if (!$model->isRoot())
{
    foreach ($model->getParentsNode() as $node) {
        $this->params['breadcrumbs'][] = ['label' => $node->name, 'url' => ['index', 'parent_id' => $node->id]];
    }
}
?>
<div class="category-index">

    <?php /* 
    <?= \yii\bootstrap\Nav::widget([
        'options' => [
            'class' => 'nav nav-tabs',
            'style' => 'margin-bottom: 15px'
        ],
        'items' => [
            [
                'label'   => 'Tabs-1',
                'url'     => ['index', 'cat' => 1],
                'active' => Yii::$app->request->get('cat') == 1,
            ],
            [
                'label'   => 'Tabs-2',
                'url'     => ['index', 'cat' => 2],
                'active' => true,
            ],
        ]
    ]) ?>
    */
 ?>
    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('创建同层分类', ['create', 'parent_id' => Yii::$app->request->get('parent_id', Category::ROOT_ID)], ['class' => 'btn btn-success']) ?>
    </p>

    <?php $form = common\wosotech\base\ActiveForm::begin(['fieldConfig' => [
        'enableLabel' => false,
    ]
    ]); ?>

    <?php $this->beginBlock('panel'); ?>
    <div class="form-group row">
        <div class="col-md-3">
            <?php echo $form->field($searchModel, 'id')->dropDownList([1 => 'YES', '0' => 'NO']) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($searchModel, 'id')->textInput(['placeholder' => 'ID']) ?>
        </div>
        <div class="col-md-3">
            <?php echo Html::submitInput('Begin', ['name' => 'begin', 'class' => 'btn btn-primary', 'data' => ['confirm' => '确认?']]) ?>
        </div>

        <?php         $js = <<<EOD
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
        'options' => ['id' => 'grid_id', 'class' => 'table-responsive'],
        'layout' => "{summary}\n{items}\n<div class='grid-panel hide'>{$this->blocks['panel']}</div>\n{pager}",
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'], 
           // ['class' => 'yii\grid\CheckboxColumn'],
                        'id',
            'name',
            //'icon',
            [
                'attribute' => 'iconThumbUrl',
                'format' => ['image', ['width'=>'32', 'height'=>'32']],
            ],

            //'keyword',
            //'description',
            'depth',
            'unit',
            [
                'attribute' => 'path',
                'visible' => YII_ENV_DEV,
            ],
             'sort_order',
             'is_visual:boolean',
             //'status',
            //'parent_id',
            'is_leaf:boolean',
            [
                //'attribute' => 'optionsAliasString',
                'attribute' => 'optionsAliasString',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->is_leaf) {
                        return Html::a('挑选规格<br/>', ['/category-option/index', 'category_id' => $model->id]) . ' ' . $model->getOptionsAliasString('<br/>');
                    }
                    return '-';
                },
                'headerOptions' => array('style' => 'width:250px;'),
            ],
             'created_at',
            // 'updated_at',
//            [
//                'attribute' => 'isLeaf',
//                'format' => 'boolean',
//                'value' => function ($model, $key, $index, $column) {
//                    return $model->isLeaf();
//                },
//                'visible' => YII_ENV_DEV,
//            ],


            [
                'label' => '查看',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    return $model->depth >= 3 ? '' : Html::a('查看下级(' . count($model->children) . ')', ['index', 'parent_id' => $model->id]);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => '{update} {view} {delete}',
                'template' => '{update} {delete}',
            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>

