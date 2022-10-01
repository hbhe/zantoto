<?php

use common\models\ShopCategory;
use common\wosotech\base\ActiveForm;
use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ShopCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '联盟门店分类';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index', 'parent_id' => 0]]; // $this->title;
if ($parent = ShopCategory::findOne(['id' => Yii::$app->request->get('parent_id')])) {
    $this->params['breadcrumbs'][] = $parent->name;
}
?>
<div class="outlet-category-index">

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
        <?php // echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
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
//            ['class' => 'yii\grid\CheckboxColumn'],
                        //'id',
            'name',
            //'parent_id',
            //'parent.name:raw:父分类',
            //'sort_order',
            [
                'label' => '子分类',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    // return Html::a(count($model->children), ['index', 'parent_id' => $model->id]);
                    return Html::a(count($model->children), ['children', 'parent_id' => $model->id]);
                },
                'visible' => !Yii::$app->request->get('parent_id'),
            ],
            //'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => '{update} {view} {delete}',
                //'template' => '{update} {delete}',
                'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{update} {view}',
            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>

    <div class="outlet-category-form">

        <?php $form = ActiveForm::begin(); ?>

        <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <div class="form-group">
            <?php echo Html::submitButton($model->isNewRecord ? '创建同级分类' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

