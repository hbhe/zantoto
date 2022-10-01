<?php

namespace backend\models;

use common\models\User;
use common\wosotech\helper\Util;
use Yii;
use yii\base\Model;

/**
 * Account form
 */
class ResetPasswordForm extends Model
{
    public $username;
    public $mobile;
    public $verify_code;
    public $password;
    public $password_confirm;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'string', 'min' => 1, 'max' => 32],
            [['mobile'],  'match' , 'pattern'=>'/^1\d{10}$/' , 'message' => '手机格式不正确'],
            [['password', 'password_confirm'], 'string', 'max' => 32],
            [['username', 'mobile', 'verify_code', 'password', 'password_confirm'], 'required'],
            [['password_confirm'], 'compare', 'compareAttribute' => 'password'],
            [['verify_code'], 'validateVerifyCode', 'skipOnEmpty' => false, 'skipOnError' => false],

        ];
    }

    public function validateVerifyCode($attribute, $params, $validator)
    {
        if ('verify_code' == $attribute) {
            if (empty($this->$attribute)) {
                $this->addError($attribute, "校验码不能为空");
            } else {
                $user = User::findByUsername($this->username);
                if (!$user) {
                    $this->addError('username', '无效的账号!');
                    return;
                }
                if ($user->mobile != $this->mobile) {
                    $this->addError('mobile', '手机号不匹配!');
                }
                if (!Util::checkVerifyCode($user->mobile, $this->$attribute)) {
                    $this->addError($attribute, "校验码不正确");
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'email' => Yii::t('backend', 'Email'),
            'password' => Yii::t('backend', 'Password'),
            'password_confirm' => Yii::t('backend', 'Password Confirm'),
            'mobile' => '手机号',
            'verify_code' => '验证码',
        ];
    }
}
