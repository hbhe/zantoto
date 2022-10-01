<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Member;
use common\models\Order;
use rest\controllers\ActiveController;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\ForbiddenHttpException;

/**
 * Class DashboardController
 * @package rest\modules\v1\controllers
 *
 * 不需登录的共公接口
 * 127.0.0.1/zantoto/rest/web/v1/site/lookup?classname=\common\models\Order&funcname=getStatusOptions3
 * 127.0.0.1/zantoto/rest/web/v1/site/get-site-settings?access-token=token-10000000002
 *
 */
class SiteController extends ActiveController
{
    public $modelClass = 'common\models\Order';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['create'], $actions['update'], $actions['delete'], $actions['view']);

        // 公共多文件上传接口
        $actions['files-upload'] = [
            'class' => UploadAction::className(),
            'multiple' => true,
            'fileparam' => 'file',
            'deleteRoute' => 'files-delete',
            'on afterSave' => function ($event) {
                $file = $event->file;
                // $img = ImageManagerStatic::make($file->read())->fit(120, 120);
                // $file->put($img->encode());
                // $model = Yii::$app->user->identity;
                // $model->detachBehavior('alipay_picture');
                // $model->alipay_path = $file->getPath();
                // $model->alipay_base_url = Yii::$app->fileStorage->baseUrl;
                // if (!$model->save()) {
                //    Yii::error([__METHOD__, __LINE__, $model->errors]);
                // }
            }
        ];

        $actions['files-delete'] = [
            'class' => DeleteAction::className(),
        ];

        $actions['oauth'] = [
            'class' => 'yii\authclient\AuthAction',
            'successCallback' => [$this, 'onAuthSuccess'],
            //'successUrl' => ['/site/contact'], // 根据code获取用户信息后, 再次跳转到应用的哪个页面, 如果不指定就跳到Yii::$app->user->returnUrl
        ];

        return $actions;
    }

    /**
     * 根据code返回openid, 注意每个code只能使用一次
     * http://mysite.com/v1/site/oauth?authclient=weixin_mp&code=xxxx
     * @param ClientInterface $client
     * @return bool|\yii\console\Response|\yii\web\Response
     */
    /*
    {
        "success": true,
        "data": {
            "openid": "oEvDz1DeOBelocjETSsA65uxxx",
            "nickname": "xxxx",
            "sex": 2,
            "language": "zh_CN",
            "city": "",
            "province": "波茨坦",
            "country": "德国",
            "headimgurl": "http://thirdwx.qlogo.cn/mmopen/v",
            "privilege": [],
            "unionid": "xxxxxx",
            "id": "xxx",
            "username": "xxxx",
            "avatar_url": "http://thirdwx.qlogo.cn/mm"
        }
    }
    */
    public function onAuthSuccess(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        Yii::info([__METHOD__, __LINE__, $attributes]);
        $response = Yii::$app->getResponse();
        $response->data = $attributes;
        // $response = new Response();
        return $response;
    }

    public function actionGetSiteSettings()
    {
        $arr['SettingsOrder_cash_feerate'] = Yii::$app->keyStorage->get('SettingsOrder.cash.feerate');
        $arr['SettingsOrder_out_energy'] = Yii::$app->keyStorage->get('SettingsOrder.out.energy');

        $d2 = new \DateTime(Yii::$app->keyStorage->get('SettingsReleaseDate', '2019-01-01 00:00:00'));
        $d1 = new \DateTime(date('Y-m-d H:i:s'));
        $diff = $d2->diff($d1);
        $arr['SettingsReleaseDate'] = $diff;

        $arr['SettingsStatActiveMember'] = $this->getSettingsStatActiveMember();
        return $arr;
    }

    public function actionGetStat()
    {
        $key = [__METHOD__];
        $arr = Yii::$app->cache->get($key);
        if ($arr !== false) {
            return $arr;
        }

        $arr['count_agent'] = 1;
        $arr['count_need'] = 2;
        $arr['count_order'] = 3;

        Yii::$app->cache->set($key, $arr, Yii::$app->params['RestCacheDuration']);
        return $arr;
    }

    public function actionLookup()
    {
        $args = Yii::$app->request->get();
        $funcname = $args['name'];
        $funcnames = [
            'get-opened-outlet-cities' => '\common\models\Outlet',
        ];
        if (empty($funcnames[$funcname])) {
            throw new ForbiddenHttpException('Invalid name');
        }
        $classname = $funcnames[$funcname];

        $funcname = lcfirst(Inflector::camelize($funcname));
        if (($params = ArrayHelper::getValue($args, 'params')) == null) {
            return call_user_func([$classname, $funcname]);
        } else {
            return call_user_func([$classname, $funcname], $params);
        }
    }

    public function optional()
    {
        return [
            'get-stat',
            'lookup',
            'oauth',
        ];
    }

}
