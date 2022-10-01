<?php
use yii\helpers\Html;

use yii\helpers\Url;

use yii\jui\JuiAsset;

use yii\web\JsExpression;

use kartik\widgets\FileInput;

use app\modules\yii2extensions\models\Image;

use wbraganca\dynamicform\DynamicFormWidget;
?>


<div id="panel-option-values" class="panel panel-default">

    <div class="panel-heading">

        <h3 class="panel-title"><i class="fa fa-check-square-o"></i> Option values</h3>

    </div>


    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper',
        'widgetBody' => '.form-options-body',
        'widgetItem' => '.form-options-item',
        'min' => 1,
        'insertButton' => '.add-item',
        'deleteButton' => '.delete-item',
        'model' => $modelsOptionValue[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            //'name',
            'option_value_name',
            //'img'
        ],

    ]); ?>


    <table class="table table-bordered table-striped margin-b-none">
        <thead>
            <tr>
                <th style="width: 90px; text-align: center"></th>
                <th class="required">Option value name</th>
                <th style="width: 188px;">Image</th>
                <th style="width: 90px; text-align: center">Actions</th>
            </tr>
        </thead>

        <tbody class="form-options-body">
            <?php foreach ($modelsOptionValue as $index => $modelOptionValue): ?>
                <tr class="form-options-item">
                    <td class="sortable-handle text-center vcenter" style="cursor: move;">
                        <i class="fa fa-arrows"></i>
                    </td>

                    <td class="vcenter">
                        <?= $form->field($modelOptionValue, "[{$index}]option_value_name")->label(false)->textInput(['maxlength' => 128]); ?>
                    </td>

                    <td>
                        <?php if (!$modelOptionValue->isNewRecord): ?>
                            <?= Html::activeHiddenInput($modelOptionValue, "[{$index}]id"); ?>
<!--                            --><?//= Html::activeHiddenInput($modelOptionValue, "[{$index}]image_id"); ?>
<!--                            --><?//= Html::activeHiddenInput($modelOptionValue, "[{$index}]deleteImg"); ?>
                        <?php endif; ?>

<!--                         --><?php
//
//                            $modelImage = Image::findOne(['id' => $modelOptionValue->image_id]);
//
//                            $initialPreview = [];
//
//                            if ($modelImage) {
//
//                                $pathImg = Yii::$app->fileStorage->baseUrl . '/' . $modelImage->path;
//
//                                $initialPreview[] = Html::img($pathImg, ['class' => 'file-preview-image']);
//
//                            }
//
//                        ?>
<!---->
<!--                        --><?//= $form->field($modelOptionValue, "[{$index}]img")->label(false)->widget(FileInput::classname(), [
//
//                            'options' => [
//
//                                'multiple' => false,
//
//                                'accept' => 'image/*',
//
//                                'class' => 'optionvalue-img'
//
//                            ],
//
//                            'pluginOptions' => [
//
//                                'previewFileType' => 'image',
//
//                                'showCaption' => false,
//
//                                'showUpload' => false,
//
//                                'browseClass' => 'btn btn-default btn-sm',
//
//                                'browseLabel' => ' Pick image',
//
//                                'browseIcon' => '<i class="glyphicon glyphicon-picture"></i>',
//
//                                'removeClass' => 'btn btn-danger btn-sm',
//
//                                'removeLabel' => ' Delete',
//
//                                'removeIcon' => '<i class="fa fa-trash"></i>',
//
//                                'previewSettings' => [
//
//                                    'image' => ['width' => '138px', 'height' => 'auto']
//
//                                ],
//
//                                'initialPreview' => $initialPreview,
//
//                                'layoutTemplates' => ['footer' => '']
//
//                            ]
//
//                        ]) ?>
<!---->
                       

                    </td>

                    <td class="text-center vcenter">

                        <button type="button" class="delete-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

        <tfoot>

            <tr>

                <td colspan="3"></td>

                <td><button type="button" class="add-item btn btn-success btn-sm"><span class="fa fa-plus"></span> New</button></td>

            </tr>

        </tfoot>

    </table>

    <?php DynamicFormWidget::end(); ?>

</div>


<?php

$js = <<<'EOD'


$(".optionvalue-img").on("filecleared", function(event) {

    var regexID = /^(.+?)([-\d-]{1,})(.+)$/i;

    var id = event.target.id;

    var matches = id.match(regexID);

    if (matches && matches.length === 4) {

        var identifiers = matches[2].split("-");

        $("#optionvalue-" + identifiers[1] + "-deleteimg").val("1");

    }

});


var fixHelperSortable = function(e, ui) {

    ui.children().each(function() {

        $(this).width($(this).width());

    });

    return ui;

};


$(".form-options-body").sortable({

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