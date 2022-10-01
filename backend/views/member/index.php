<?php

use yii\helpers\Html;
use common\wosotech\base\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-index">

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
            //['class' => 'yii\grid\CheckboxColumn'],
            [
                'attribute' => 'avatarImageUrl',
                'format' => ['image', ['width'=>'32', 'height'=>'32',  'class' => "img-responsive img-circle"]],
            ],

                        'id',
            //'sid',
            //'username',
            'mobile',
            'nickname',
            // 'auth_key',
            // 'access_token',
            // 'password_plain',
            // 'password_hash',
            // 'email:email',
            // 'status',
            // 'gender',
            // 'area_parent_id',
            // 'area_id',
            // 'age',
            // 'avatar_path',
            // 'avatar_base_url:url',
            // 'updated_at',
            // 'logged_at',
            'pid',
            // 'status_bind',
            // 'status_audit',
            // 'openid',
            // 'weixin_number',
            // 'card_id',
            // 'card_name',
            // 'card_branch',
            // 'card_bank',
            // 'alipay_id',
            // 'alipay_name',
            // 'balance_revenue',
            // 'balance_power',
            // 'balance_fish',
            // 'balance_coupon',
//            'is_real_name:boolean',
//            'identity',
            'name',
//            'is_star:boolean',
            'is_seller:boolean',
  //          'is_outlet:boolean',
            [
                'label' => '账户余额',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    $str = Html::a(Yii::$app->formatter->asCurrency($model->balance_revenue), ['revenue-log/index', 'member_id' => $model->id]) . ' ';
                    return $str;
                },
                'headerOptions' => array('style' => 'width:90px;'),
                'visible' => YII_ENV_DEV,
            ],

            'created_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => YII_ENV_DEV ? '{update} {view} {delete}' : '{update} {view}',
                // 'template' => '{update} {view}',
            ]
        ],
    ]); ?>

    <?php common\wosotech\base\ActiveForm::end(); ?>


</div>
