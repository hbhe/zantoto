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

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_inner',
    'widgetBody' => '.container-rooms',
    'widgetItem' => '.room-item',
    'limit' => 20,
    'min' => 1,
    'insertButton' => '.add-room',
    'deleteButton' => '.remove-room',
    'model' => $modelsProductOptionValue[0],
    'formId' => 'dynamic-form',
    'formFields' => [
        'name',
        'price',
        'sort_order',
    ],
]); ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>值</th>
            <th>价格增加</th>
            <th>排序</th>
            <th class="text-center">
                <button type="button" class="add-room btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
            </th>
        </tr>
        </thead>
        <tbody class="container-rooms">
        <?php foreach ($modelsProductOptionValue as $indexProductOptionValue => $modelProductOptionValue): ?>
            <tr class="room-item">
                <td class="vcenter">
                    <?php
                    if (! $modelProductOptionValue->isNewRecord) {
                        echo Html::activeHiddenInput($modelProductOptionValue, "[{$indexProductOption}][{$indexProductOptionValue}]id");
                    }
                    ?>
                    <?= $form->field($modelProductOptionValue, "[{$indexProductOption}][{$indexProductOptionValue}]name")->label(false)->textInput(['maxlength' => true]) ?>
                </td>
                <td class="vcenter">
                    <?= $form->field($modelProductOptionValue, "[{$indexProductOption}][{$indexProductOptionValue}]price")->label(false)->textInput(['maxlength' => true]) ?>
                </td>
                <td class="vcenter">
                    <?= $form->field($modelProductOptionValue, "[{$indexProductOption}][{$indexProductOptionValue}]sort_order")->label(false)->textInput(['maxlength' => true]) ?>
                </td>

                <td class="text-center vcenter" style="width: 90px;">
                    <button type="button" class="remove-room btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus"></span></button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php DynamicFormWidget::end(); ?>