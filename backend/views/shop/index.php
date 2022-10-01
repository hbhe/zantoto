<?php

use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SellerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seller-index">

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
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <? if (YII_ENV_DEV): ?>
        <?php echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
        <? endif; ?>
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
           // ['class' => 'yii\grid\CheckboxColumn'],
                        'id',
            //'logo',
            [
                'attribute' => 'logoThumbUrl',
                'format' => ['image', ['width'=>'32', 'height'=>'32']],
            ],

            'member_id',
            'member.nickname:raw:昵称',
            'title',
            //'catString',
            'catPathString',
            //'company',
            // 'credit_code',
//             'area_parent_id',
//             'area_id',
//             'district_id',
            'districtAreaCode.pathName:raw:地区',
            // 'address',
            // 'open_time',
            // 'tel',
            // 'detail',
            // 'legal_person',
            // 'legal_identity',
            // 'business_licence_image',
            // 'identity_face_image',
            // 'identity_back_image',
             //'seller_status',
             'sellerStatusString',
             'seller_time',
            // 'seller_reason',
            // 'sort_order',
            //'statusString',
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => YII_ENV_DEV ? '{view} {update} {delete}' : '{view} {delete}',
                //'template' => '{update} {delete}',
            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>

