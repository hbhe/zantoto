<?php

use common\models\Banner;
use yii\helpers\Html;
use common\wosotech\base\GridView;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BannerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'BANNER管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= \yii\bootstrap\Nav::widget([
    'options' => [
        'class' => 'nav nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => Banner::getNavItems(),
])
?>

<div class="banner-index">

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
        <?php echo Html::a('创建', ['create', 'cat' => Yii::$app->request->get('cat')], ['class' => 'btn btn-success']) ?>
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

            [
                'attribute' => 'id',
                'headerOptions' => array('style' => 'width:60px;'),
                'filter' => false,
                //'visible' => YII_DEBUG,
            ],

            [
                'attribute' => 'imageUrl',
                'format' => ['image', ['width' => '48',]], //'height'=>'32'
            ],

            //'cat',
            'title',
            [
                'attribute' => 'detail',
                'value'=>function ($model, $key, $index, $column) {
                    return StringHelper::truncate(strip_tags($model->detail), 50);
                },
            ],
            //'detail:ntext',
//            'img_id',
//             'img_url:url',
             'jumpTypeString',
             'url:url',
             //'app_function_id',
             'appFunctionString',
             //'second',
             'sort_order',
             'status:boolean',
             'created_at',
             'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => '{update} {view} {delete}',
                'template' => '{update} {delete}',
            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>


