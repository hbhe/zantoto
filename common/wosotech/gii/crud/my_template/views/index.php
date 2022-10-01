<?php

use common\wosotech\base\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "common\\wosotech\\base\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <?= "<?php /* \n"; ?>
    <?= "<?= " ?>\yii\bootstrap\Nav::widget([
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
    <?= "*/\n ?>"; ?>

    <h1 style="display:none;"><?= "<?= " ?>Html::encode($this->title) ?></h1>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?= "<?php echo " ?>Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : '' ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>

    <?= "<?php " ?>$form = common\wosotech\base\ActiveForm::begin(['fieldConfig' => [
        'enableLabel' => false,
    ]
    ]); ?>

    <?= "<?php " ?>$this->beginBlock('panel'); ?>
    <div class="form-group row">
        <div class="col-md-3">
            <?= "<?php echo " ?>$form->field($searchModel, 'id')->dropDownList([1 => 'YES', '0' => 'NO']) ?>
        </div>
        <div class="col-md-3">
            <?= "<?php echo " ?>$form->field($searchModel, 'id')->textInput(['placeholder' => 'ID']) ?>
        </div>
        <div class="col-md-3">
            <?= "<?php echo " ?>Html::submitInput('Begin', ['name' => 'begin', 'class' => 'btn btn-primary', 'data' => ['confirm' => '确认?']]) ?>
        </div>

        <?= "<?php " ?>
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
    <?= "<?php " ?>$this->endBlock(); ?>

    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'grid_id', 'class' => 'table-responsive'],
        'layout' => "{summary}\n{items}\n<div class='grid-panel hide'>{$this->blocks['panel']}</div>\n{pager}",
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            <?php echo "//['class' => 'yii\grid\SerialColumn'], \n" ?>
            <?php echo "['class' => 'yii\grid\CheckboxColumn'], \n" ?>
            <?php
            $count = 0;
            if (($tableSchema = $generator->getTableSchema()) === false) {
                foreach ($generator->getColumnNames() as $name) {
                    if (++$count < 6) {
                        echo "            '" . $name . "',\n";
                    } else {
                        echo "            // '" . $name . "',\n";
                    }
                }
            } else {
                foreach ($tableSchema->columns as $column) {
                    $format = $generator->generateColumnFormat($column);
                    if (++$count < 6) {
                        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                    } else {
                        echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                    }
                }
            }
            ?>

            [
                'class' => 'yii\grid\ActionColumn',
                //'template' => '{update} {view} {delete}',
                'template' => '{update} {delete}',
            ]
        ],
    ]); ?>

    <?= "<?php " ?>common\wosotech\base\ActiveForm::end(); ?>


<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>
<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>
</div>


<?php echo "<?php\n"; ?> /*
<?= "<?= " ?> GridView::widget([
	'layout' => "<div>{summary}\n{items}\n{pager}</div>",
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'options' => ['class' => 'table-responsive'],
	'tableOptions' => ['class' => 'table table-striped'],  
	'columns' => [     

		['class' => yii\grid\CheckboxColumn::className()],
        [
            'attribute' => 'id',
            'headerOptions' => array('style' => 'width:60px;'),
        ],

        'created_at:date',  
		[
			'label' => 'Office',
			'value' => function ($model, $key, $index, $column) {
				return empty($model->office->title) ? '' : $model->office->title;
				return Yii::$app->formatter->asCurrency($model->amount/100);
				return MItem::getItemCatName($model->cid);
				return "￥".sprintf("%0.2f", $model->feesum/100);
			},
			'filter' => false,
            'format' => 'currency',
			'filter' => MItem::getItemCatName(),
			'headerOptions' => array('style'=>'width:80px;'),    
			'visible' => Yii::$app->user->identity->openid == 'admin',
		],

        [
            'attribute' => 'image_url',
            'format' => ['image', ['width'=>'32', 'height'=>'32']],
        ],

        [
            'attribute' => 'photo_id',
            'format' => ['image', ['width'=>'32', 'height'=>'32']],
            'value' => function ($model, $key, $index, $column) {
                return \Yii::$app->imagemanager->getImagePath($model->photo_id);
            },
        ],

        [
            'label' => 'Shop',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                return Html::a($model->sid, 'http://baidu.com', array("target" => "_blank"));
            },
        ],

        [
            'label' => 'avator',
            'format' => 'html',
            'value' => function ($model, $key, $index, $column) {
                if (empty($model->wxUser->headimgurl))
                    return '';
                $headimgurl = Html::img(\common\wosotech\Util::getWxUserHeadimgurl($model->wxUser->headimgurl, 46), ['class' => "img-responsive img-circle"]);
                return Html::a($headimgurl, ['/xg-member/index', 'openid' => $model->openid]);
            },
        ],

		[
			'label' => 'Post Image',
			'format' => 'html',
			'value' => function ($model, $key, $index, $column) {
				return Html::a($model->postResponseCount, ['post-response/index', 'post_id'=>$model->id]);
				return Html::a(Html::img(Url::to($model->getPicUrl()), ['width'=>'75']), $model->getPicUrl());
			},
		],

		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{update} {view} {delete}',
			'options' => ['style'=>'width: 100px;'],
			'buttons' => [
				'update' => function ($url, $model) {
					return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
						'class' => 'btn btn-xs btn-primary',
						'title' => Yii::t('plugin', 'Update'),
					]);
				},
				'view' => function ($url, $model) {
					return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url, [
						'class' => 'btn btn-xs btn-warning',
						'title' => Yii::t('plugin', 'View'),
					]);
				},
				'delete' => function ($url, $model) {
					return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
						'class' => 'btn btn-xs btn-danger',
						'data-method' => 'post',
						'data-confirm' => Yii::t('plugin', 'Are you sure to delete this item?'),
						'title' => Yii::t('plugin', 'Delete'),
						'data-pjax' => '0',
					]);
				},
			]
		],

        [
            'class' => '\hbhe\grid\ToggleColumn',
            'attribute' => 'status',
            'action' => 'toggle-status',
            'onText' => '禁用',
            'offText' => '启用',
            'displayValueText' => true,
            'onValueText' => '已禁用',
            'offValueText' => '已启用',
            'iconOn' => 'stop',
            'iconOff' => 'stop',
            // Uncomment if  you don't want AJAX
            'enableAjax' => false, // 使用pjax时要注掉或设为true
            //'visible' => YII_ENV_DEV,
            'confirm' => function($model, $toggle) {
                if ($model->status == 1) {
                    return "确认启用: {$model->id}?";
                } else {
                    return "确认禁用: {$model->id}?";
                }
            },
            'headerOptions' => array('style' => 'width:80px;'),
        ],

	]
]);

*/

