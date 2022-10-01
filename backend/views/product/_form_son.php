<?php

use common\models\Category;
use common\models\Product;
use common\wosotech\helper\Util;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;
use yii\jui\JuiAsset;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper',
    'widgetBody' => '.container-items',
    'widgetItem' => '.house-item',
    'limit' => 10,
    'min' => 0,
    'insertButton' => '.add-house',
    'deleteButton' => '.remove-house',
    'model' => $modelsProductOption[0],
    'formId' => 'dynamic-form',
    'formFields' => [
        'name',
    ],
]); ?>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="width: 90px; text-align: center"></th>

            <th>规格</th>
            <th style="width: 450px;">值</th>
            <th class="text-center" style="width: 90px;">
                <button type="button" class="add-house btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
            </th>
        </tr>
        </thead>
        <tbody class="container-items">
        <?php foreach ($modelsProductOption as $indexProductOption => $modelProductOption): ?>
            <tr class="house-item">
                <td class="sortable-handle text-center vcenter" style="cursor: move;">
                    <i class="fa fa-arrows"></i>
                </td>

                <td class="vcenter">
                    <?php
                    // necessary for update action.
                    if (! $modelProductOption->isNewRecord) {
                        echo Html::activeHiddenInput($modelProductOption, "[{$indexProductOption}]id");
                    }
                    ?>
                    <?= $form->field($modelProductOption, "[{$indexProductOption}]name")->label(false)->textInput(['maxlength' => true]) ?>
                </td>
                <td>
                    <?= $this->render('_form_grandson', [
                        'form' => $form,
                        'indexProductOption' => $indexProductOption,
                        'modelsProductOptionValue' => $modelsProductOptionValue[$indexProductOption],
                    ]) ?>
                </td>
                <td class="text-center vcenter" style="width: 90px; verti">
                    <button type="button" class="remove-house btn btn-danger btn-xs"><span class="fa fa-minus"></span></button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php DynamicFormWidget::end(); ?>

<?php

$js = <<<'EOD'
var fixHelperSortable = function(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
};


$(".container-items").sortable({
    items: "tr",
    cursor: "move",
    opacity: 0.6,
    axis: "y",
    handle: ".sortable-handle",
    helper: fixHelperSortable,
    update: function(ev){
        $(".dynamicform_wrapper").yiiDynamicForm("updateContainer");
    }
}).disableSelection();


EOD;


JuiAsset::register($this);

$this->registerJs($js);

?>
