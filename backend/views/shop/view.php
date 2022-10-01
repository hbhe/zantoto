<?php

use common\models\Seller;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Seller */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '商户', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="seller-view">

        <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

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
                'id',
                'member_id',
                'member.nickname:raw:昵称',
                //'cat',
                'catPathString',
                'title',
                'company',
                'credit_code',
//            'area_parent_id',
//            'area_id',
//            'district_id',
                'districtAreaCode.pathName:raw:地区',
                'address',
                //'open_time',
                //'tel',
                'detail',
                'legal_person',
                'legal_identity',
//                'business_licence_image',
//                'identity_face_image',
//                'identity_back_image',
                //'logo',
//            [
//                'attribute' => 'logoThumbUrl',
//                'format' => ['image', ['width'=>'32', 'height'=>'32']],
//            ],

                [
                    'attribute' => 'logoThumbUrl',
                    'format' => 'raw',
                    'value' => function ($model, $widget) {
                        $items[] = [
                            'url' => $model->logoUrl,
                            'src' => $model->logoThumbUrl,
                            'options' => array('title' => ''),
                            'imageOptions' => ['width' => '100', 'class' => "img-responsive"],
                        ];
                        return dosamigos\gallery\Gallery::widget(['items' => $items,
                            'templateOptions' => ['id' => 'logoThumbUrl'],
                            'clientOptions' => ['container' => '#logoThumbUrl'],
                        ]);
                    },
                ],

                [
                    'attribute' => 'licenceThumbUrl',
                    'format' => 'raw',
                    'value' => function ($model, $widget) {
                        $items[] = [
                            'url' => $model->licenceUrl,
                            'src' => $model->licenceThumbUrl,
                            'options' => array('title' => ''),
                            'imageOptions' => ['width' => '100', 'class' => "img-responsive"],
                        ];
                        return dosamigos\gallery\Gallery::widget(['items' => $items,
                            'templateOptions' => ['id' => 'licenceThumbUrl'],
                            'clientOptions' => ['container' => '#licenceThumbUrl'],
                        ]);
                    },
                ],

                [
                    'attribute' => 'faceThumbUrl',
                    'format' => 'raw',
                    'value' => function ($model, $widget) {
                        $items[] = [
                            'url' => $model->faceUrl,
                            'src' => $model->faceThumbUrl,
                            'options' => array('title' => ''),
                            'imageOptions' => ['width' => '100', 'class' => "img-responsive"],
                        ];
                        return dosamigos\gallery\Gallery::widget(['items' => $items,
                            'templateOptions' => ['id' => 'faceThumbUrl'],
                            'clientOptions' => ['container' => '#faceThumbUrl'],
                        ]);
                    },
                ],

                [
                    'attribute' => 'backThumbUrl',
                    'format' => 'raw',
                    'value' => function ($model, $widget) {
                        $items[] = [
                            'url' => $model->backUrl,
                            'src' => $model->backThumbUrl,
                            'options' => array('title' => ''),
                            'imageOptions' => ['width' => '100', 'class' => "img-responsive"],
                        ];
                        return dosamigos\gallery\Gallery::widget(['items' => $items,
                            'templateOptions' => ['id' => 'backThumbUrl'],
                            'clientOptions' => ['container' => '#backThumbUrl'],
                        ]);
                    },
                ],

                //'seller_status',
                'sellerStatusString',
                'seller_time',
                'seller_reason',
                'sort_order',
                //'status',
                'statusString',
                'created_at',
                'updated_at',
                'order_count_daily',
                'order_amount_daily',
                'order_count_total',
                'order_amount_total',
            ],
        ]) ?>


        <?php $form = common\wosotech\base\ActiveForm::begin(['fieldConfig' => [
            'enableLabel' => false,
        ]
        ]); ?>

        <?php if (in_array($model->seller_status, [Seller::SELLER_STATUS_WAIT, Seller::SELLER_STATUS_REFUSED])): ?>
            <div class="form-group row">
                <div class="col-md-12">
                    <?php echo Html::submitInput('通过', ['name' => 'accept', 'class' => 'btn btn-primary', 'data' => ['confirm' => '确认通过?']]) ?>
                    <?php echo Html::submitInput('拒绝', ['name' => 'refuse', 'class' => 'btn btn-info', 'data' => ['confirm' => '确认拒绝?']]) ?>
                    <br/><br/>
                    <?php echo $form->field($model, 'seller_reason')->inline()->textInput(['placeholder' => '拒绝理由']) ?>
                </div>
            </div>
        <?php else: ?>

            <?php if (in_array($model->status, [Seller::STATUS_OK])): ?>
                <div class="form-group row">
                    <div class="col-md-12">
                        <?php echo Html::submitInput('关店', ['name' => 'stop', 'class' => 'btn btn-danger', 'data' => ['confirm' => '确认关店?']]) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($model->status, [Seller::STATUS_STOP])): ?>
                <div class="form-group row">
                    <div class="col-md-12">
                        <?php echo Html::submitInput('开店', ['name' => 'start', 'class' => 'btn btn-info', 'data' => ['confirm' => '确认开店?']]) ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php common\wosotech\base\ActiveForm::end(); ?>
    </div>


<?php //echo Html::img($model->logoUrl); ?>
