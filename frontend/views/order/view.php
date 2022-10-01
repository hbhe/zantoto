<?php

use common\models\MemberOrder;
use common\wosotech\base\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '订单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="order-view">

        <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

        <p>
            <?php //echo Html::a('接单人数:' . $model->memberOrdersCount, ['/member-order/index', 'order_id' => $model->id], ['class' => 'btn btn-success']) ?>
        </p>

        <p style="display:none;">
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'id',
                    'captionOptions' => ['width' => '20%'],
                ],
                'parentAreaCodeName',
                'areaCodeName',
                'title',
                'orderTagsString',
                'skillTagsString',
                'brandTagsString',
                'detail:ntext',
                //'logo',
                'amount',
                'start_date',
                'days',
                'headcount',
                'statusString',
                'created_at',
                //'updated_at',
                'memo',

                'member_id',
                'mobile:raw:发单用户手机号',
                'name:raw:发单用户姓名',
                'member.parentAreaCodeName:raw:发单用户省份',
                'member.areaCodeName:raw:发单用户城市',
            ],
        ]) ?>
<br/>
<!--        <h3>发单用户信息</h3>-->
        <?php /* echo DetailView::widget([
            'model' => $member,
            'attributes' => [
                [
                    'attribute' => 'id',
                    'captionOptions' => ['width' => '20%'],
                ],
                'name',
                //'sid',
                'mobile',
//                'age',
//                'genderString',
//            'nickname',
//            'password_plain',
//            'password_hash',
//            'email:email',

//                'skillTagsString',
//                'brandTagsString',
                'parentAreaCodeName',
                'areaCodeName',

//                'created_at',
//                'updated_at',

//                'pid',
//                'parentName',
//                'parentMobile',
//                'avatarImageUrl',
//                [
//                    'attribute' => 'avatarImageUrl',
//                    'format' => ['image', ['width' => '100', 'height' => '100', 'class' => "img-responsive img-circle"]],
//                ],
//
//                'statusString',
//                'statusBindString',
//                'statusAuditString',
//
            ],
        ])
        */ ?>


        <?php $form = common\wosotech\base\ActiveForm::begin(['fieldConfig' => [
            'enableLabel' => false,
        ]
        ]); ?>

        <?php $this->beginBlock('panel'); ?>
        <div class="form-group row">
            <!--        <div class="col-md-3">-->
            <!--            --><?php //echo $form->field($searchModel, 'id')->dropDownList([1 => 'YES', '0' => 'NO']) ?>
            <!--        </div>-->
            <!--        <div class="col-md-3">-->
            <!--            --><?php //echo $form->field($searchModel, 'id')->textInput(['placeholder' => 'ID']) ?>
            <!--        </div>-->
            <div class="col-md-12">
                <?php echo Html::submitInput('交付完成', ['name' => 'finish', 'class' => 'btn btn-primary', 'data' => ['confirm' => '确认交付完成?']]) ?>
                <?php // echo Html::submitInput('驳回', ['name' => 'refuse', 'class' => 'btn btn-primary', 'data' => ['confirm' => '确认驳回?']]) ?>
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
            // $this->registerJs($js, yii\web\View::POS_READY);
            ?>
        </div>
        <?php $this->endBlock(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['id' => 'grid_id', 'class' => 'table-responsive-x'],
            'layout' => "{summary}\n{items}\n<div class='grid-panel'>{$this->blocks['panel']}</div>\n{pager}",
            //'layout' => "{summary}\n{items}\n{pager}",
            //'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                //['class' => 'yii\grid\CheckboxColumn'],
                //'id',
                'order_id',
                [
                    'attribute' => 'order.title',
                    'headerOptions' => array('style'=>'width:200px;'),
                ],
                'member_id',
                'member.name',
                //'statusString',
                [
                    'attribute' => 'statusString',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $str = $model->statusString;
                        if ($model->status == MemberOrder::STATUS_WAIT) {
                            $str .= Html::a(' 指派', ['/member-order/handle', 'id' => $model->id, 'status' => MemberOrder::STATUS_ASSIGNED], ['data' => ['confirm' => '确认指派?', 'method' => 'post']]);
                            $str .= Html::a(' 不匹配', ['/member-order/handle', 'id' => $model->id, 'status' => MemberOrder::STATUS_REFUSED], ['data' => ['confirm' => '确认不匹配?', 'method' => 'post']]);
                        }
                        if ($model->status == MemberOrder::STATUS_SUBMITTED) {
                            $str .= Html::a(' 驳回', ['/member-order/handle', 'id' => $model->id, 'status' => MemberOrder::STATUS_SUBMITTED_REFUSED], ['data' => ['confirm' => '确认驳回?', 'method' => 'post']]);
                        }

                        return $str;
                    },
                    'headerOptions' => array('style' => 'width:180px;'),

                ],

                'created_at',
                // 'updated_at',
                // 'memo',
                // 'attachment',
                // 'params:ntext',
                [
                    'label' => ' ',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        $str = Html::a('查看订单', ['/order/view', 'id' => $model->order_id]);
                        return $str;
                    },
                    'headerOptions' => array('style' => 'width:100px;'),
                    'visible' => false,
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'controller' => 'member-order',
                    'template' => '{update} {view} {delete}',
                ]
            ],
        ]); ?>

        <?php common\wosotech\base\ActiveForm::end(); ?>

    </div>


<?php
/*
<?=  DetailView::widget([
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
