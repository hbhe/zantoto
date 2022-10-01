<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\ResetPasswordForm */

$this->title = '忘记密码';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<script type="text/javascript">
    $(document).ready(function () {
        var ajaxUrl = "<?= Url::to(['/site/ajax-broker']); ?>";
        var cnt = 60;
        $("#sendcode").click(function(){

            // alert(ret['mobile']);
            var mobile = $("#mobile").val();
            var args = {
                'classname': '-common-wosotech-helper-Util',
                'funcname': 'sendVerifycodeAjax',
                'params': {
                    'mobile': mobile,
                    'template': 'SMS_001'
                }
            };

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                cache: false,
                dataType: 'json',
                data: "args=" + encodeURIComponent(JSON.stringify(args)),
                success: function (ret) {
                    if(0 == ret['code'])
                    {
                        //location.reload();
                        alert('验证码发送成功！');
                        $("#sendcode").attr("disabled", true);
                        changeResendBtn();
                    }
                    else
                    {
                        alert(ret['msg']);
                    }

                },
                error: function () {
                }
            });
/*
            var args1 = {
                'classname': '-common-models-User',
                'funcname': 'getMobile',
                'params': {
                    'username': $("#username").val(),
                    'password': $("#password").val()
                }
            };

            alert(ajaxUrl);
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                cache: false,
                dataType: 'json',
                data: "args=" + encodeURIComponent(JSON.stringify(args1)),
                success: function (ret) {
                    alert(222);
                    if(0 == ret['code'])
                    {

                    }
                    else
                    {
                        alert(ret['msg']);
                    }
                },
                error: function () {
                    alert(999);
                }
            });
*/
            return false;

        })

        function changeResendBtn()
        {
            $("#sendcode").val('获取验证码 ' + cnt + 's');
            cnt --;
            if(cnt<1)
            {
                $("#sendcode").val('获取验证码 ');
                $("#sendcode").attr("disabled",false);
                clearTimeout(sto);
                cnt = 60;
            }
            else
            {
                sto = setTimeout(changeResendBtn, 1000);
            }
        }

    });

</script>

<div class="login-box" style="width:600px;">
    <div class="login-logo">
        <a href="#">重置密码</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg hide">Sign in to start your session</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?php echo $form->field($model, 'username')->textInput(['id' => 'username', 'maxlength' => 16]) ?>
        <?php echo $form->field($model, 'mobile')->textInput(['id' => 'mobile', 'maxlength' => 16]) ?>
        <?php echo $form->field($model, 'verify_code')->textInput(['maxlength' => true]) ?>
        <input type="button" id="sendcode" class="btn btn-success" name="sendcode" value="获取验证码">
        <br/>
        <br/>

        <?php echo $form->field($model, 'password')->passwordInput() ?>
        <?php echo $form->field($model, 'password_confirm')->passwordInput() ?>

        <div class="row">
            <div class="col-xs-12">
                <?= Html::submitButton('保存', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>


        <?php ActiveForm::end(); ?>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
