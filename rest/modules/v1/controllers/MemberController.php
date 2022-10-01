<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

//use Intervention\Image\ImageManagerStatic;
use common\models\ActiveRecord;
use common\models\Auth;
use common\models\FishLog;
use common\models\MemberFollow;
use common\models\PowerEvent;
use common\models\PowerLog;
use common\models\Todo;
use common\wosotech\helper\CaptchaHelper;
use common\wosotech\helper\Util;
use Intervention\Image\ImageManagerStatic;
use rest\controllers\ActiveController;
use rest\models\Member;
use rest\models\MemberSearch;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class MemberController
 * @package rest\modules\v1\controllers
 *
 *
 * 手机验证码登录
 * http://127.0.0.1/zantoto/rest/web/v1/members/login-by-verify-code?mobile=13900000001&verify_code=3456
 *
 * 发送短信校验码
 * 127.0.0.1/zantoto/rest/web/v1/members/send-verify-code?mobile=15527210477&template=SMS_001
 *
 * 注册(个人或代理)
 * POST 127.0.0.1/zantoto/rest/web/v1/members
 *
 * 个人公开信息
 * 127.0.0.1/zantoto/rest/web/v1/members/26614866
 *
 * 我的信息
 * 127.0.0.1/zantoto/rest/web/v1/members/me?expand=photos&access-token=token-13900000001
 * 127.0.0.1/zantoto/rest/web/v1/members/me?expand=photos,skillTags,brandTags&access-token=token-13900000001
 * 127.0.0.1/zantoto/rest/web/v1/members/me?expand=photos&access-token=token-13900000001
 * 127.0.0.1/zantoto/rest/web/v1/members/me?access-token=token-13900000001
 *
 *
 * 修改个人信息
 * PUT 127.0.0.1/zantoto/rest/web/v1/members/20336405?access-token=token-13900000001
 *
 * 上传头像
 * POST 127.0.0.1/zantoto/rest/web/v1/members/avatar-update?access-token=token-13900000001
 *
 * 上传收款码
 * POST 127.0.0.1/zantoto/rest/web/v1/members/alipay-update?access-token=token-13900000001
 *
 * 第三方账号登录
 * http://127.0.0.1/zantoto/rest/web/v1/members/login-by-openid?oauth_client=weixin&oauth_client_user_id=weixin-13900000001
 *
 * 将第三方账号绑定一个已经存在的账号
 * POST http://127.0.0.1/zantoto/rest/web/v1/members/openid-bind-mobile
 *
 * 手机或ID密码登录
 * http://127.0.0.1/zantoto/rest/web/v1/members/login?mobile=13900000001&password=123456
 *
 * 127.0.0.1/zantoto/rest/web/v1/members/about-me?access-token=13900000001
 *
 * 验证短信校验码
 * 127.0.0.1/zantoto/rest/web/v1/members/validate-verify-code?mobile=13900000001&verify_code=1234
 *
 * 修改密码
 * PUT 127.0.0.1/zantoto/rest/web/v1/members/update-password?access-token=token-13900000001
 *
 * 忘记密码重置
 * PUT 127.0.0.1/zantoto/rest/web/v1/members/reset-password
 *
 */
class MemberController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = 'rest\models\Member';

    /**
     * @var string
     */
    public $searchModelClass = 'rest\models\MemberSearch';

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['view']);

        $actions['avatar-update'] = [
            'class' => UploadAction::className(),
            'multiple' => false,
            'fileparam' => 'file',
            'deleteRoute' => 'avatar-delete',
            'on afterSave' => function ($event) {
                $file = $event->file;
                $img = ImageManagerStatic::make($file->read())->fit(120, 120);
                $file->put($img->encode());
                $model = Yii::$app->user->identity;
                $model->detachBehavior('picture');
                $model->avatar_path = $file->getPath();
                $model->avatar_base_url = Yii::$app->fileStorage->baseUrl;
                if (!$model->save()) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                }
                $model->dropPower(PowerEvent::ID_UPLOAD_AVATAR);
            }
        ];

        $actions['avatar-delete'] = [
            'class' => DeleteAction::className(),
        ];

        $actions['weixin-update'] = [
            'class' => UploadAction::className(),
            'multiple' => false,
            'fileparam' => 'file',
            'deleteRoute' => 'weixin-delete',
            'on afterSave' => function ($event) {
                $file = $event->file;
                // $img = ImageManagerStatic::make($file->read())->fit(120, 120);
                // $file->put($img->encode());
                $model = Yii::$app->user->identity;
                $model->detachBehavior('weixin_picture');
                $model->weixin_qrcode_path = $file->getPath();
                $model->weixin_qrcode_base_url = Yii::$app->fileStorage->baseUrl;
                if (!$model->save()) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                }
            }
        ];

        $actions['weixin-delete'] = [
            'class' => DeleteAction::className(),
        ];

        return $actions;
    }

    /**
     * @param $action
     * @return array
     */
    public function prepareDataProvider($action)
    {
        return false;

        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        return $dataProvider;
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     * @throws Exception
     */
    public function actionLogin()
    {
        $mobile = Yii::$app->request->post('mobile');
        $password = Yii::$app->request->post('password');

        $model = Member::find()
            ->andWhere(['or', ['mobile' => $mobile], ['id' => $mobile]])
            ->one();
        if (null === $model) {
            throw new ForbiddenHttpException('无此账号');
        }

        if (\common\models\Member::STATUS_NOT_ACTIVE === $model->status) {
            throw new ForbiddenHttpException('账号已冻结');
        }

        if (!$model->validatePassword($password)) {
            throw new ForbiddenHttpException('密码不正确');
        }

        // 重置token, 踢掉已登录者
//        $model->updateAttributes([
//            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
//        ]);
        $model->setScenario('login');
        return $model;
    }

    public function actionLoginByVerifyCode()
    {
        $mobile = Yii::$app->request->post('mobile');
        $verify_code = Yii::$app->request->post('verify_code');
        $pid = Yii::$app->request->post('pid');

        if (!Util::checkVerifyCode($mobile, $verify_code)) {
            throw new ForbiddenHttpException('校验码不正确');
        }

        $model = Member::find()
            ->andWhere(['or', ['mobile' => $mobile], ['id' => $mobile]])
            ->one();
        if (null === $model) {
            $model = new \common\models\Member();
            $model->load(Yii::$app->request->post(), '');
            $model->mobile = $mobile;
            $model->id = Member::generateId();
            if (!$model->save()) {
                throw new ForbiddenHttpException('创建账号失败!');
            }
        }
        if (\common\models\Member::STATUS_NOT_ACTIVE === $model->status) {
            throw new ForbiddenHttpException('账号已冻结');
        }

        $model->setScenario('login');
        return $model;
    }

    /**
     * 发送校验码
     * @return mixed
     * @throws Exception
     */
    public function actionSendVerifyCode()
    {
        $mobile = Yii::$app->request->post('mobile') ?: Yii::$app->request->get('mobile');
        $template = Yii::$app->request->post('template') ?: Yii::$app->request->get('template', 'SMS_001');
        $params = [
            'mobile' => $mobile,
            'template' => $template,
        ];
        $resp = Json::decode(Util::sendVerifycodeAjax($params), true);
        if ($resp['code'] != 0) {
            throw new ForbiddenHttpException($resp['msg']);
        }
        return $resp;
    }

    /**
     * 我的信息
     * @return Member
     */
    public function actionMe()
    {
        $model = $this->findModel(Yii::$app->user->id);
        $model->scenario = ActiveRecord::SCENARIO_VIEW;
        return $model;
    }

    /**
     * 注册
     * @return array|Member
     * http://127.0.0.1/zantoto/backend/web/site/get-rest-image-captcha
     */
    public function actionCreate()
    {
        $model = new \rest\models\Member();
        $params = Yii::$app->request->post();
        $model->id = Member::generateId();
        if (!empty($params['pid'])) {
            $ar = Member::find()->where(['or', ['id' => $params['pid']], ['sid' => $params['pid']]])->one();
            if ($ar === null) {
                throw new ForbiddenHttpException('无效的推荐人ID1');
            }
            $params['pid'] = $ar->id;
        }

        if ($model->load($params, '') && $model->validate()) {
            if (empty($model->verify_code)) {
                throw new ForbiddenHttpException('短信校验码不能为空');
            }
            if (!Util::checkVerifyCode($model->mobile, $model->verify_code)) {
                throw new ForbiddenHttpException('短信校验码不正确');
            }

//            if (empty($model->image_verify_code)) {
//                throw new ForbiddenHttpException('图形校验码不能为空');
//            }
//
//            $helper = new CaptchaHelper();
//            if (!$helper->verify($model->image_verify_code)) {
//                throw new ForbiddenHttpException('图形校验码不正确');
//            }
//

            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                return $model;
            }
        } else {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
            return $model;
        }
        $arr = $model->toArray();
        return $arr;
    }

    /**
     * 检查校验码
     * @return mixed
     * @throws Exception
     */
    public function actionValidateVerifyCode()
    {
        $mobile = Yii::$app->request->post('mobile') ?: Yii::$app->request->get('mobile');
        $verify_code = Yii::$app->request->post('verify_code') ?: Yii::$app->request->get('verify_code');
        if (!Util::checkVerifyCode($mobile, $verify_code)) {
            throw new ForbiddenHttpException('校验码不正确');
        }
        return ['code' => 0];
    }

    /**
     * 修改个人信息, 如呢称, 手机(需验证码+收货人姓名), 银行卡, 支付宝, 微信号, 通知设置
     * @param $id
     * @return Member
     */

    public function actionUpdate($id)
    {
        if ($id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update own account');
        }
        $model = $this->findModel($id);
        $old_mobile = $model->mobile;
        $params = Yii::$app->request->post();
        foreach ($params as $key => $value) {
            // 只允许修改昵称和手机号
            if (!in_array($key, ['shipping_name', 'need_message', 'need_message_new_fan', 'need_message_thumbup', 'need_message_remark', 'mobile', 'verify_code', 'nickname', 'card_id', 'card_name', 'card_branch', 'card_bank', 'alipay_id', 'alipay_name', 'weixin_number'])) {
                unset($params[$key]);
            }
        }
        if ($model->load($params, '') && $model->validate()) {
            // 如果手机号发生变化, 就检查手机校验码
            if ($old_mobile != $model->mobile) {
                if (empty($model->verify_code) || !Util::checkVerifyCode($model->mobile, $model->verify_code)) {
                    throw new ForbiddenHttpException('手机校验码不正确');
                }
                $shipping_name = ArrayHelper::getValue($params, 'shipping_name');
                if (empty($shipping_name)) {
                    throw new ForbiddenHttpException('收货人姓名不能为空');
                }
                // $ar = $model->getMemberAddresses()->andWhere(['name' => $shipping_name])->one();
                $ar = $model->getBuyOrders()->andWhere(['shipping_name' => $shipping_name])->limit(1)->one();
                if (empty($ar)) {
                    throw new ForbiddenHttpException('无效的收货人姓名');
                }

            }
            $model->save(false);
        }

        return $model;
    }

    /**
     * 实名认证
     * @return \common\models\Member
     * @throws ForbiddenHttpException
     */
    public function actionUpdateName()
    {
        $model = $this->findModel(Yii::$app->user->id);
        if ($model->is_real_name) {
            throw new ForbiddenHttpException('已通过实名认证,不能修改');
        }

        $key_count_check_identity = [__METHOD__, Yii::$app->user->id];
        $count_check_identity = Yii::$app->cache->get($key_count_check_identity);
        if ($count_check_identity === false) {
            $count_check_identity = 5;
        }

        if ($count_check_identity <= 0) {
            throw new ForbiddenHttpException('认证次数已超限！');
        }

        $name = Yii::$app->request->post('name');
        $identity = Yii::$app->request->post('identity');
        if (Member::find()->where(['identity' => $identity])->limit(1)->one()) {
            throw new ForbiddenHttpException('身份证ID已被占用');
        }

        if (!Util::identityIsTrue($identity, $name)) {
            $count_check_identity -= 1;
            Yii::$app->cache->set($key_count_check_identity, $count_check_identity, 24 * 3600); // 24小时内限试5次
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
            }
            throw new ForbiddenHttpException("实名认证失败! 剩余认证次数:{$count_check_identity}!");
        }
        $model->identity = $identity;
        $model->name = $name;
        $model->is_real_name = 1;
        if (!$model->save(false)) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $model;
    }

    /**
     * 未登录状态下重置密码
     * @param $id
     * @return Member
     */
    public function actionResetPassword()
    {
        $mobile = Yii::$app->request->post('mobile');
        $verify_code = Yii::$app->request->post('verify_code');
        $password = Yii::$app->request->post('password');
        $model = Member::findOne(['mobile' => Yii::$app->request->post('mobile')]);
        if ($model === null) {
            throw new NotFoundHttpException('无效的用户');
        }
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            if (empty($verify_code) || !Util::checkVerifyCode($model->mobile, $verify_code)) {
                $model->addError('mobile', "手机校验码不正确");
                return $model;
            }
            if (empty($password)) {
                $model->addError('password', "密码不能为空!");
                return $model;
            }

            if ($model->password) {
                $model->setPassword($model->password);
            }
            $model->save(false);
        }

        return $model;
    }

    /**
     * 登录之后修改自己的密码, 必须提供老密码和新密码
     * @param $id
     * @return Member
     */
    public function actionUpdatePassword()
    {
        $model = Member::findOne(Yii::$app->user->id);
        $mobile = $model->mobile;
        if (empty($mobile)) {
            throw new ForbiddenHttpException('手机号尚未设置');
        }
        $verify_code = Yii::$app->request->post('verify_code');
        $new_password = Yii::$app->request->post('new_password');
        if (empty($verify_code) || empty($new_password)) {
            throw new HttpException('400', '验证证或密码不能为空');
        }
        if (!Util::checkVerifyCode($mobile, $verify_code)) {
            throw new ForbiddenHttpException('校验码不正确');
        }
        $model->setPassword($new_password);
        if (!$model->save(false)) {
            throw new ForbiddenHttpException('校验码不正xxx确');
        }
        return $model;
    }

    /**
     * @param $id
     * @return \common\models\Member
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Member::findOne($id)) === null) {
            throw new NotFoundHttpException('此用户ID不存在.');
        }
        return $model;
    }

    /**
     * @param $id
     * @return \common\models\Member
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    /**
     * 使用第三方账号登录
     * @return array|null|\yii\db\ActiveRecord
     * @throws Exception
     */
    public function actionLoginByOpenid()
    {
        $oauth_client = Yii::$app->request->post('oauth_client') ?: Yii::$app->request->get('oauth_client');
        $oauth_client_user_id = Yii::$app->request->post('oauth_client_user_id') ?: Yii::$app->request->get('oauth_client_user_id');
        if (empty($oauth_client) || empty($oauth_client_user_id)) {
            throw new ForbiddenHttpException('无效的参数!');
        }
        $auth = Auth::find()->where([
            // 'oauth_client' => $oauth_client,
            'oauth_client_user_id' => $oauth_client_user_id
        ])->one();

        if (empty($auth->member)) {
            throw new ForbiddenHttpException('尚未绑定账号!');
        }
        $model = $auth->member;
        $model->scenario = 'login';
        return $model;
    }

    /**
     * 将openid与一个已有账号进行绑定
     * @return array|null|\yii\db\ActiveRecord
     * @throws Exception
     */
    /*
    当访问一个需要登录才能访问的页面时，跳到登录页, 进行以下检查
    如果是微信环境
    1. 拼装公众号授权URL地址，浏览器跳转过去让用户授权，授权后浏览器会跳回来, 拿到code，调用后台接口（传入weixin_mp + code参数）获取到unionid + openid
    2. 调用第三方登录接口进行登录(传入weixin_mp + unionid）
    3. 如果登录成功，前端保存用户信息，标为已登录, 访问首页或者要访问的页面; 否则打开绑定页面, 用户输入手机号、验证码，调用后台绑定接口, 如果后台找到此手机号码，就进行绑定，返回用户信息给前端，前端保存用户信息，标为已登录；如果后台找不到此手机号码，就直接创建手机账号并绑定，返回前端用户信息, 前端保存用户信息，标为已登录

    如果是非微信环境，如果用户输入账号密码登录，就走正常流程; 如果用户点"微信登录"按钮, 则
    1. 拼装网站应用微信登录授权URL地址，浏览器跳转过去让用户授权，授权后浏览器会跳回来, 拿到code，调用后台接口（传入weixin_web + code参数）获取unionid+openi
    2. 调用第三方登录接口进行登录(传入weixin_web + unionid)
    3. 同上面的3

    当用户输入手机号码进行绑定时，后台发现此手机号码账号存在就直接绑定，返回用户信息；如果发现手机号码不存在，就先创建此手机号码账号再绑定，返回账号信息给前端。

    这么做其实就是把绑定跟注册合在一块了，后端保证绑定必须成功。
    */
    public function actionOpenidBindMobile()
    {
        $oauth_client = Yii::$app->request->post('oauth_client');
        $oauth_client_user_id = Yii::$app->request->post('oauth_client_user_id');
        $nickname = Yii::$app->request->post('nickname');
        $avatar_url = Yii::$app->request->post('avatar_url');
        $mobile = Yii::$app->request->post('mobile');
        $openid = Yii::$app->request->post('openid');
        $verify_code = Yii::$app->request->post('verify_code');
        $pid = Yii::$app->request->post('pid');

        if (empty($oauth_client)) {
            throw new ForbiddenHttpException('无效的渠道');
        }

        if (empty($verify_code)) {
            throw new ForbiddenHttpException('无效的校验码');
        }

        if (!Util::checkVerifyCode($mobile, $verify_code)) {
            throw new ForbiddenHttpException('校验码不匹配');
        }

        $model = Member::findOne(['mobile' => $mobile]);
        if (null === $model) {
            // 绑定时如手机账号不存在, 则创建账号
            $model = new Member();
            $model->attributes = [
                'id' => Member::generateId(),
                'mobile' => $mobile,
                'nickname' => $nickname,
                'avatar_base_url' => $avatar_url,
                'pid' => $pid,
            ];
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                throw new ForbiddenHttpException('保存错误');
            }
        }

        $auth = Auth::findOne(['oauth_client' => $oauth_client, 'oauth_client_user_id' => $oauth_client_user_id]);
        if (null === $auth) {
            $auth = new Auth();
        }
        $auth->attributes = [
            'oauth_client' => $oauth_client,
            'oauth_client_user_id' => $oauth_client_user_id,
            'nickname' => $nickname,
            'avatar_url' => $avatar_url,
            'user_id' => $model->id,
            'openid' => $openid,
        ];
        if (!$auth->save()) {
            Yii::error([__METHOD__, __LINE__, $auth->errors]);
        }

        // 如果是公众号, 记录openid, 好做消息推送
        if ($oauth_client == 'weixin_mp') {
            $model->status_bind = 1;
            $model->openid = $openid;
            $model->save(false);
        }

        $model->scenario = 'login';
        return $model;
    }

    /**
     * 已登录用户在个人中心直接绑定
     * @return null|Member|static
     * @throws ForbiddenHttpException
     */
    public function actionDirectBind()
    {
        $oauth_client = Yii::$app->request->post('oauth_client');
        $oauth_client_user_id = Yii::$app->request->post('oauth_client_user_id');
        $openid = Yii::$app->request->post('openid'); // 如果是微信公众号,可传openid这个参数进行保存
        if (empty($oauth_client)) {
            throw new ForbiddenHttpException('无效的渠道');
        }
        $model = Member::findOne(Yii::$app->user->id);

        $auth = Auth::findOne(['oauth_client' => $oauth_client, 'oauth_client_user_id' => $oauth_client_user_id]);
        if (null !== $auth) {
            throw new ForbiddenHttpException('此微信账号已在绑定中');
        }
        $auth = new Auth();
        $auth->load(Yii::$app->request->post(), '');
        $auth->user_id = Yii::$app->user->id;
        if (!$auth->save()) {
            Yii::error([__METHOD__, __LINE__, $auth->errors]);
        }

        // 如果是微信公众号, 记录一下openid方便模板消息推送
        if ($oauth_client == 'weixin_mp') {
            $model->updateAttributes(['status_bind' => 1, 'openid' => $openid]);
        }

        return $auth;
    }

    /**
     * 已登录用户在个人中心直接解绑
     * @return null|Member|static
     * @throws ForbiddenHttpException
     */
    public function actionDirectUnbind()
    {
        $oauth_client = Yii::$app->request->post('oauth_client');
        if (empty($oauth_client)) {
            throw new ForbiddenHttpException('无效的渠道');
        }
        $auth = Auth::findOne(['oauth_client' => $oauth_client, 'user_id' => Yii::$app->user->id]);
        if ($auth === null) {
            throw new ForbiddenHttpException('未曾绑定过此渠道');
        }
        $auth->delete();
        return $auth;
    }

    /**
     * 获取某渠道是否已绑定
     * @return array
     */
    public function actionCheckBind()
    {
        $oauth_client = Yii::$app->request->get('oauth_client');
        if (empty($oauth_client)) {
            throw new ForbiddenHttpException('无效的渠道');
        }
        $auth = Auth::findOne(['oauth_client' => $oauth_client, 'user_id' => Yii::$app->user->id]);
        return [
            'is_bind' => $auth === null ? 0 : 1,
            'auth' => $auth,
        ];
    }

    /*
     * 定义不登录也可访问的actions
     */
    public function optional()
    {
        return [
            'index',
            'view',
            'create',
            'login',
            'login-by-verify-code',
            'login-by-openid',
            'openid-bind-mobile',
            'reset-password',
            'send-verify-code',
            'validate-verify-code',
        ];
    }

}

