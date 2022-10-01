<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

    <h1 style="display:none;"><?= "<?= " ?>Html::encode($this->title) ?></h1>

    <p style="display:none;">
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Update') ?>, ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Delete') ?>, ['delete', <?= $urlParams ?>], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => <?= $generator->generateString('Are you sure you want to delete this item?') ?>,
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= "<?= " ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
        ],
    ]) ?>

</div>

<?php echo "<?php\n"; ?>
/*
<?= "<?= " ?> DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'id',
            'captionOptions' => ['width' => '20%'],
        ],
        [
            'attribute' => 'check_status',
            'value' => common\models\MktPostComplain::getPostComplainOption($model->reason) ,
        ],
        [
            'attribute' => 'imageUrl',
            'format' => 'raw',
            'value' => Html::a(Html::img($model->imageUrl, ['width' => 100]), $model->imageUrl, ["target" => "_blank"]),
        ],
        [
            'label' => '图片',
            //'value' => "<img src='". \Yii::$app->imagemanager->getImagePath($model->img_id, 160, 80, 'inset') ."'>",
            //'format'=> 'html',
            'value' => \Yii::$app->imagemanager->getImagePath($model->img_id, 160, 80, 'inset'),
            'format' => ['image', ['width'=>'100','height'=>'100']],
        ],
        [
            'attribute' => 'logo_id',
            'value' => $model->getLogoUrl(),
            'format' => ['image'],
            //'format' => ['image', ['width'=>'100','height'=>'100']],
        ],
    ],
])
*/
