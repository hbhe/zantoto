<?php
namespace backend\models;

use common\models\User;
use common\wosotech\helper\Util;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $verify_code;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            [['verify_code'], 'validateVerifyCode', 'skipOnEmpty' => false, 'skipOnError' => false],

        ];
    }

    public function validateVerifyCode($attribute, $params, $validator)
    {
        if ('verify_code' == $attribute) {
            if (empty($this->$attribute)) {
                $this->addError($attribute, "校验码不能为空");
            } else {
                $user = $this->getUser();
                if (!$user) {
                    $this->addError('username', '无效的账号!');
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
            'username' => '账号',
            'password' => '密码',
            'verify_code' => '验证码',
            'rememberMe' => '记住密码',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', '账号密码不正确或者账号被冻结!');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
