<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '用户', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-view">

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
            'sid',
            'username',
            'mobile',
            'name',
            'nickname',
            'access_token',
//            'email:email',
            'status',
//            'gender',
//            'area_parent_id',
//            'area_id',
//            'age',
//            'avatar_path',
//            'avatar_base_url:url',
            'avatarImageUrl',
            'created_at',
            'updated_at',
            'logged_at',
            'pid',
            'is_seller:boolean',
            'balance_revenue',
            'memberProfile.is_real_name:boolean',
            'memberProfile.identity',
//            'memberProfile.card_id',
//            'memberProfile.card_name',
//            'memberProfile.card_branch',
//            'memberProfile.card_bank',
//            'memberProfile.alipay_id',
//            'memberProfile.alipay_name',
//            'balance_coupon',
        ],
    ]) ?>

</div>
