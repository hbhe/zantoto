<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'identity')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div style="color:#999;margin:1em 0">
                    If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <div class="form-group">
                <?php $authAuthChoice = yii\authclient\widgets\AuthChoice::begin([
                    'baseAuthUrl' => ['/site/oauth']
                ]); ?>
                <ul>
                    <a href="<?= $authAuthChoice->createClientUrl($authAuthChoice->clients['qq']) ?>"><span class="fa fa-qq"> QQ</span></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?= $authAuthChoice->createClientUrl($authAuthChoice->clients['weixin_web']) ?>"><span class="fa fa-weixin"> Weixin-WEB</span></a>

                    <a href="<?= $authAuthChoice->createClientUrl($authAuthChoice->clients['weixin_mp']) ?>"><span class="fa fa-weixin"> Weixin-MP</span></a>

                    <a href="<?= $authAuthChoice->createClientUrl($authAuthChoice->clients['github']) ?>"><span class="fa fa-github"> Github</span></a>

                </ul>
                <?php yii\authclient\widgets\AuthChoice::end(); ?>
            </div>

            <?php echo Yii::$app->authClientCollection->clients['weixin_mp']->buildAuthUrl() ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
