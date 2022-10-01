<?php

use common\models\ShopCategory;
use yii\bootstrap\ActiveForm;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use unclead\multipleinput\TabularColumn;

$this->title = '联盟门店分类';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index', 'parent_id' => 0]]; // $this->title;
if ($parent = ShopCategory::findOne(['id' => Yii::$app->request->get('parent_id')])) {
    $this->params['breadcrumbs'][] = $parent->name;
}


/* @var $this \yii\web\View */
/* @var $models ShopCategory[] */
?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id' => 'tabular-form',
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]) ?>

<?= TabularInput::widget([
    'models' => $models,
    'modelClass' => ShopCategory::class,
    'cloneButton' => true,
    'sortable' => true,
    'min' => 0,
    'iconSource' => TabularInput::ICONS_SOURCE_FONTAWESOME,
    'addButtonPosition' => [
        TabularInput::POS_HEADER,
        // TabularInput::POS_FOOTER,
        // TabularInput::POS_ROW
    ],
    'layoutConfig' => [
        'offsetClass'   => 'col-sm-offset-4',
        'labelClass'    => 'col-sm-2',
        'wrapperClass'  => 'col-sm-10',
        'errorClass'    => 'col-sm-4'
    ],
    'attributeOptions' => [
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
        'validateOnChange'       => false,
        'validateOnSubmit'       => true,
        'validateOnBlur'         => false,
    ],
    'form' => $form,
    'columns' => [
        [
            'name' => 'id',
            //'type' => TabularColumn::TYPE_TEXT_INPUT
            'type' => TabularColumn::TYPE_HIDDEN_INPUT
        ],
        [
            'name' => 'name',
            'title' => '名称',
            'type' => TabularColumn::TYPE_TEXT_INPUT,
            'options' => ['maxlength' => 32],
            'attributeOptions' => [
                'enableClientValidation' => true,
                'validateOnChange' => true,
            ],
            'defaultValue' => '',
            'enableError' => true
        ],
/*
        [
            'name' => 'sort_order',
            'title' => 'sort_order',
            'type' => TabularColumn::TYPE_STATIC,

            'attributeOptions' => [
                'enableClientValidation' => true,
                'validateOnChange' => true,
            ],
            'defaultValue' => 0,
            'enableError' => true
        ],
*/

/*
        [
            'name' => 'date',
            'type'  => '\kartik\date\DatePicker',
            'title' => 'Day',
            'defaultValue' => '1970/01/01',
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy',
                    'todayHighlight' => true,
                ],

            ],
            'headerOptions' => [
                'style' => 'width: 250px;',
                'class' => 'day-css-class'
            ]
        ],
*/
    ],
]) ?>


<div class="form-group">
    <?php echo Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
    <?php echo Html::submitInput('返回', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>
