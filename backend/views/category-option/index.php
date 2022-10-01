<?php

use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategoryOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '关联的规格';
$this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['category/index', 'parent_id' => $category->parent_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-option-index">

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
            //['class' => 'yii\grid\CheckboxColumn'],
            //'id',
            //'category_id',
            //'option_id',
            'option.name:raw:规格',
            //'option.alias:raw:规格',
            'option.valuesString:raw:取值范围',
            //'status',
            //'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => '{update} {view} {delete}',
                'template' => '{update} {delete}',
            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>
<?= $this->render('_form_inline', [
    'category' => $category,
    'model' => $model,
]) ?>

