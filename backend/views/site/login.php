<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';

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
        // 先获取手机号, 再发检验码
        $("#sendcode").click(function(){
            var args1 = {
                'classname': '-common-models-User',
                'funcname': 'getMobile',
                'params': {
                    'username': $("#username").val(),
                    'password': $("#password").val()
                }
            };

            // alert(ajaxUrl);
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                cache: false,
                dataType: 'json',
                data: "args=" + encodeURIComponent(JSON.stringify(args1)),
                success: function (ret) {
                    // alert(222);
                    if(0 == ret['code'])
                    {
                        // alert(ret['mobile']);
                        var mobile = ret['mobile'];
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

                    }
                    else
                    {
                        alert(ret['msg']);
                    }
                },
                error: function () {
                    alert('error');
                }
            });

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

<div class="login-box">
    <div class="login-logo">
        <a href="#"><span><?= Yii::$app->name; ?></span></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg hide">Sign in to start your session</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['id' => 'username', 'placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['id' => 'password', 'placeholder' => $model->getAttributeLabel('password')]) ?>

        <?php echo $form->field($model, 'verify_code')->textInput(['maxlength' => true]) ?>
        <input type="button" id="sendcode" class="btn btn-success" name="sendcode" value="获取验证码">

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <a class="hide" href="<?= Url::to(['/site/reset-password']) ?> ">忘记密码</a>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">

                <?= Html::submitButton('登录', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>

        <div class="hide">
        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in
                using Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign
                in using Google+</a>
        </div>
        <!-- /.social-auth-links -->

        <a href="#">I forgot my password</a><br>
        <a href="register.html" class="text-center">Register a new membership</a>
        </div>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
