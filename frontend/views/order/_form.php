<?php

use common\models\AreaCode;
use common\models\Order;
use common\models\Tag;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use common\wosotech\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\bootstrap\ActiveForm */
?>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#status").change(function () {
                var status = $("#status").val();
                if (status == "<?php echo Order::STATUS_REFUSED; ?>") {
                    $("#reason").show();
                } else {
                    $("#reason").hide();
                }
            }).change();
        });
    </script>

<div class="order-form">

    <?php $form = ActiveForm::begin(['enableClientScript' => false]); ?>

<!--    --><?php //echo $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'member_id')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?php //echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<!---->
    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'order_tags')->inline()->checkboxList(Tag::getTagsOption(Tag::CAT_ORDER)); ?>

    <?php echo $form->field($model, 'skill_tags')->inline()->checkboxList(Tag::getTagsOption(Tag::CAT_SKILL)); ?>

    <?php echo $form->field($model, 'brand_tags')->inline()->checkboxList(Tag::getTagsOption(Tag::CAT_BRAND)); ?>

    <?php echo $form->field($model, 'area_parent_id')->dropDownList(AreaCode::getProvinceOption(), ['prompt' => '选择...', 'id' => 'parent_id']); ?>

    <?php echo Html::hiddenInput('selected_id', $model->isNewRecord ? '' : $model->area_id, ['id' => 'selected_id']); ?>
    <?php echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options' => ['id' => 'area_id', 'class' => '', 'style' => ''],
        'pluginOptions' => [
            'depends' => ['parent_id'],
            'placeholder' => '选择...',
            'initialize' => $model->isNewRecord ? false : true,
            'url' => Url::to(['/area-code/subcat']),
            'params' => ['selected_id']
        ],
    ]); ?>

    <?php echo $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'headcount')->textInput() ?>


    <?php //echo $form->field($model, 'logo')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'start_date')->widget(\kartik\widgets\DatePicker::classname(), [
        //'options' => ['placeholder' => 'Select date ...'],
        //'type' => \kartik\widgets\DatePicker::TYPE_INPUT,
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'autoclose' => true,
            'todayHighlight' => true
        ]
    ]); ?>
    <?php echo $form->field($model, 'days')->textInput() ?>


<!--    --><?php //echo $form->field($model, 'detail')->textarea(['rows' => 6]) ?>
    <?php echo $form->field($model, 'detail')->widget(\dosamigos\tinymce\TinyMce::className(), [
        'id' => 'DESC_b_1_5_PLAIN_LAYOUT',
        'options' => [
            'rows' => 8,
        ],
        'language' => 'zh_CN',
        'clientOptions' => [
            'relative_urls' => false,
            'remove_script_host' => false,
            'convert_urls' => true,
            'file_browser_callback' => new yii\web\JsExpression("function(field_name, url, type, win) {
            window.open('" . yii\helpers\Url::to(['/imagemanager/manager', 'view-mode' => 'iframe', 'select-type' => 'tinymce']) . "&tag_name='+field_name,'','width=800,height=540 ,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no');
        }"),
            'plugins' => [
                "advlist autolink lists link charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste image"
            ],
            'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
        ]
    ]); ?>


    <?php echo $form->field($model, 'status')->dropDownList(Order::getStatusOptions(), ['id' => 'status']) ?>

    <div id="reason">
    <?php echo $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>
    </div>

<!--    --><?php //echo $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?php //echo $form->field($model, 'updated_at')->textInput() ?>
<!---->

    <?php echo $form->field($model, 'pictures')->widget(\trntv\filekit\widget\Upload::classname(), [
        'url' => ['picture-upload'],
        'multiple' => true,
        'maxNumberOfFiles' => 4,
        'sortable' => true,
        'maxFileSize' => 10 * 1024 * 1024, // 10 MiB
        //'clientOptions' => [ ...other blueimp options... ]
    ]) ?>

    <div class="form-group">
        <?php //if ($model->isNewRecord): ?>
        <?php echo Html::submitInput('提交', ['name' => 'submit', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('无审核提交', ['name' => 'nowait', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php //endif; ?>

        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

/*

<?php $this->params['backgroundColor'] = "#ffffff"; ?>
<?php $this->registerJsFile(Yii::$app->getRequest()->baseUrl.'/resources/js/mobiscroll.min.js', ['position'=>yii\web\View::POS_HEAD, 'depends'=>['\frontend\assets\MktWapCommonAsset'] ]); ?>

<?php
$css = <<<EOD
  .sample {font-size: 16px;color:#ef4f4f;}
EOD;
$this->registerCss($css);
?>

<?php
$js = <<<EOD
$cat = Html::getInputId($model,'cat');
$catName = Html::getInputName($model,'cat');    
$is_map = Html::getInputId($model,'is_map');
$is_mapName = Html::getInputName($model,'is_map');    
$("#$cat, #$is_map").change( function() {     
    var cat = $('input:radio[name="$catName"]:checked').val();
    var is_map = $('input:radio[name="$is_mapName"]:checked').val();
    $(".ship, .rate, .trade, .collect").hide();
    if (cat == '0') {
        $(".ship").show();
        $('#label_row_cnt').text('item counts');
    }
}).change();

$('.tag_ship_example').click(function() {
    editor_ship.insertText($(this).attr("alt"));
    return false;
});
EOD;
$this->registerJs($js, yii\web\View::POS_READY);
?>

<div class="my-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{input}",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
            'options' => ['tag' => false],
        ],
    ]); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'sender_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'is_anoymous')->checkbox(common\wosotech\Util::getYesNoOptionName()) ?>

    <?php echo $form->field($model, 'job_experience')->dropDownList(common\models\MktPost::getPostJobExperienceOption()) ?>

    <?php echo $form->field($model, 'category_id')->dropDownList(\yii\helpers\ArrayHelper::map(
        \common\models\ArticleCategory::find()->where(['status'=>\common\models\ArticleCategory::STATUS_ACTIVE])->all(),
        'id',
        'title'
    ), ['prompt'=>'']) ?>

    <?php echo $form->field($model, 'on_quiet')->checkbox() ?>

    <?php echo $form->field($model, 'create_time')->widget('trntv\yii\datetime\DateTimeWidget', [
            //'phpDatetimeFormat' => "YYYY-MM-DD h:mm:ss",
            'momentDatetimeFormat' => 'YYYY-MM-DD HH:mm:ss',
            'clientOptions' => [
                'minDate' => new \yii\web\JsExpression('new Date("2015-01-01")'),
                'allowInputToggle' => false,
                'sideBySide' => true,
                'locale' => 'zh-cn',
                'widgetPositioning' => [
                   'horizontal' => 'auto',
                   'vertical' => 'auto'
                ]
            ]
        ]); 
    ?>
    
    <?php echo $form->field($model, 'parent_industry_id')->dropDownList(\yii\helpers\ArrayHelper::map(
        \common\models\MktIndustry::find()->children()->all(),
        'id',
        'title'
    ), ['prompt'=>'Select Category', 'id'=>'parent_industry_id'])->label('Category'); ?>

    <?php echo $form->field($model, 'industry_id')->label('Child Category')->widget(\kartik\depdrop\DepDrop::classname(), [
         'options' => ['id' => 'industry_id'],
         'pluginOptions'=>[
             'depends'=>['parent_industry_id'],
             'placeholder' => 'Select Child Category',
             'url' => Url::to(['industry/subcat'])
         ]
     ]); ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

*/
