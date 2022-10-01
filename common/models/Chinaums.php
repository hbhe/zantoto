<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class Chinaums
 * @package common\models
 *
 * $a =  Yii::$app->em->getToken(); Yii::info($a);
 * $a =  Yii::$app->em->getUsers(2);
 * $a =  Yii::$app->em->createUser("zhangsan7","123456", 'nick1');
 * $a =  Yii::$app->em->editNickname("zhangsan7","newnick");
 * $a =  Yii::$app->em->resetPassword("zhangsan7","newpasswordxx");
 * $a =  Yii::$app->em->deleteUser("zhangsan7");
 * $a =  Yii::$app->em->deleteUsers(3);
 * $a =  Yii::$app->em->getUserToken('zhangsan7', 'newpasswordxx');
 * $a =  Yii::$app->em->getUser('zhangsan7');
 * $a =  Yii::$app->em->getChatRecord('');
 * $a =  Yii::$app->em->getChatRecord("select+*+where+timestamp>1435536480000");
 * $a =  Yii::$app->em->getChatRecordForPage("select+*+where+timestamp>1435536480000", 5);
 * $a = Yii::$app->em->sendText("admin", 'users', ['zhangsan7', 'zhangsan5'], 'hello', ['a' => 1, 'b' => 2]);
 * $a = Yii::$app->em->sendCmd("admin", 'users', ['zhangsan7', 'zhangsan5'], 'CMD_DEMO', ['a' => 1, 'b' => 2]);
 *
 */
class Chinaums extends Component
{
    public $client_id;

    public $client_secret;

    public $org_name;

    public $app_name;

    public $url;

    /**
     * 是否每次请求强制获取新的access token
     * @var boolean
     */
    public $forceRefreshToken = false;

    /**
     * 数据缓存前缀
     * @var string
     */
    public $cachePrefix = 'cache_yii2_easemob_sdk';

    /**
     * 缓存对象
     * @var \yii\caching\Cache
     */
    public $cache;

    /**
     * API路径前缀
     * @var string
     */
    public $apiBaseUrl = 'http://a1.easemob.com'; // https://a1.easemob.com

    /**
     * HTTP Client
     * @var \GuzzleHttp\Client
     */
    public $apiClient;

    /**
     * access token信息
     * @var array
     */
    protected $token;

    public $mid = '898201612345678'; // 商户号
    public $instMid = 'APPDEFAULT'; // 机构商户号
    public $tid = '12345080'; // 终端号

    const DEBUG = 1;

    public $weixinUrl = self::DEBUG ? 'http://58.247.0.18:29015/v1/netpay/wx/unified-order' : 'https://api-mop.chinaums.com/v1/netpay/wx/unified-order';
    public $alipayUrl = self::DEBUG ? 'http://58.247.0.18:29015/v1/netpay/trade/precreate' : 'https://api-mop.chinaums.com/v1/netpay/trade/precreate';
    public $qmfUrl = self::DEBUG ? 'http://58.247.0.18:29015/v1/netpay/qmf/order' : 'https://api-mop.chinaums.com/v1/netpay/qmf/order';
    public $uacUrl = self::DEBUG ? 'http://58.247.0.18:29015/v1/netpay/uac/app-order' : 'https://api-mop.chinaums.com/v1/netpay/uac/app-order';

    public $queryUrl = self::DEBUG ? 'http://58.247.0.18:29015/v1/netpay/query' : 'https://api-mop.chinaums.com/v1/netpay/query';

    public $refundUrl = self::DEBUG ? 'http://58.247.0.18:29015/v1/netpay/refund' : 'https://api-mop.chinaums.com/v1/netpay/refund';

    public $refundQueryUrl = self::DEBUG ? 'http://58.247.0.18:29015/v1/netpay/refund-query' : 'https://api-mop.chinaums.com/v1/netpay/refund-query';

    public function init()
    {
        parent::init();
        if (!isset($this->org_name)) {
            throw new InvalidConfigException('请先配置企业的唯一标识');
        }
        if (!isset($this->app_name)) {
            throw new InvalidConfigException('请先配置应用名称');
        }
        if (!isset($this->client_id)) {
            throw new InvalidConfigException('请先配置Client Id');
        }
        if (!isset($this->client_secret)) {
            throw new InvalidConfigException('请先配置Client Secret');
        }

        if (!empty ($this->org_name) && !empty ($this->app_name)) {
            $this->url = $this->apiBaseUrl . '/' . $this->org_name . '/' . $this->app_name . '/';
        }

        if (Yii::$app->cache === null) {
            $this->cache = Yii::createObject([
                'class' => 'yii\caching\FileCache',
            ]);
        } else {
            $this->cache = Yii::$app->cache;
        }
    }

    /**
     * 下单
     * @param $data
     * @return bool|mixed
     */
    function createPay($data)
    {
        $data['mid'] = $this->mid;
        $data['instMid'] = $this->instMid;
        $data['tid'] = $this->tid;
        $data['requestTimestamp'] = date('Y-m-d H:i:s');

        $url = $this->weixinUrl;
        $body = json_encode($data);
        //$header = array($this->getToken());
        $header = [];
        $result = $this->postCurl($url, $body, $header);
        var_dump($result);
        Yii::info($result);
        if (ArrayHelper::getValue($result, 'errCode') != 'SUCCESS') {
            // var_dump($result);
            Yii::error([__METHOD__, __LINE__, $result]);
            // throw new HttpException(500, $result['error']);
            return false;
        }
        return $result;
    }

    function QueryPay($data)
    {
        $data['mid'] = $this->mid;
        $data['instMid'] = $this->instMid;
        $data['tid'] = $this->tid;
        $data['requestTimestamp'] = date('Y-m-d H:i:s');

        $url = $this->queryUrl;
        $body = json_encode($data);
        //$header = array($this->getToken());
        $header = [];
        $result = $this->postCurl($url, $body, $header);
        var_dump($result);
        Yii::info($result);
        if (ArrayHelper::getValue($result, 'errCode') != 'SUCCESS') {
            // var_dump($result);
            Yii::error([__METHOD__, __LINE__, $result]);
            // throw new HttpException(500, $result['error']);
            return false;
        }
        return $result;
    }

    function createRefund($data)
    {
        $data['mid'] = $this->mid;
        $data['instMid'] = $this->instMid;
        $data['tid'] = $this->tid;
        $data['requestTimestamp'] = date('Y-m-d H:i:s');

        $url = $this->refundUrl;
        $body = json_encode($data);
        //$header = array($this->getToken());
        $header = [];
        $result = $this->postCurl($url, $body, $header);
        var_dump($result);
        Yii::info($result);
        if (ArrayHelper::getValue($result, 'errCode') != 'SUCCESS') {
            // var_dump($result);
            Yii::error([__METHOD__, __LINE__, $result]);
            // throw new HttpException(500, $result['error']);
            return false;
        }
        return $result;
    }

    function QueryRefund($data)
    {
        $data['mid'] = $this->mid;
        $data['instMid'] = $this->instMid;
        $data['tid'] = $this->tid;
        $data['requestTimestamp'] = date('Y-m-d H:i:s');

        $url = $this->refundQueryUrl;
        $body = json_encode($data);
        //$header = array($this->getToken());
        $header = [];
        $result = $this->postCurl($url, $body, $header);
        var_dump($result);
        Yii::info($result);
        if (ArrayHelper::getValue($result, 'errCode') != 'SUCCESS') {
            // var_dump($result);
            Yii::error([__METHOD__, __LINE__, $result]);
            // throw new HttpException(500, $result['error']);
            return false;
        }
        return $result;
    }

    /**
     * 获取请求token
     */
    function getToken()
    {
        $cacheToken = $this->getCache(__METHOD__);
        if ($this->forceRefreshToken || empty($cacheToken)) {
            $options = array(
                "grant_type" => "client_credentials",
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret
            );
            $body = json_encode($options);
            $url = $this->url . 'token';
            $cacheToken = $this->postCurl($url, $body, $header = array());
            $cacheToken['expires_at'] = time() + $cacheToken['expires_in'] - 600;
            $this->setCache(__METHOD__, $cacheToken, $cacheToken['expires_in']);
        }
        // var_dump($cacheToken['expires_in']);
        // return $cacheToken;
        return "Authorization:Bearer " . $cacheToken['access_token'];
    }

    /**
     *$this->postCurl方法
     */
    function postCurl($url, $body, $header, $type = "POST")
    {
        // var_dump($url);
        // var_dump($body);

        //1.创建一个curl资源
        $ch = curl_init();
        //2.设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);//设置url
        //1)设置请求头
        //array_push($header, 'Accept:application/json');
        //array_push($header,'Content-Type:application/json');
        //array_push($header, 'http:multipart/form-data');
        //设置为false,只会获得响应的正文(true的话会连响应头一并获取到)
        curl_setopt($ch, CURLOPT_HEADER, 0);
//		curl_setopt ( $ch, CURLOPT_TIMEOUT,5); // 设置超时限制防止死循环
        //设置发起连接前的等待时间，如果设置为0，则无限等待。
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //2)设备请求体
        // if (count($body) > 0)
        if (strlen($body) > 0) {
            //$b=json_encode($body,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);//全部数据使用HTTP协议中的"POST"操作来发送。
        }
        //设置请求头
        if (count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        //上传文件相关设置
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算

        //3)设置提交方式
        switch ($type) {
            case "GET":
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case "PUT"://使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请求。这对于执行"DELETE" 或者其他更隐蔽的HTT
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }


        //4)在HTTP请求中包含一个"User-Agent: "头的字符串。-----必设

//		curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
//		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)'); // 模拟用户使用的浏览器
        //5)


        //3.抓取URL并把它传递给浏览器
        $res = curl_exec($ch);

        $result = json_decode($res, true);
        //4.关闭curl资源，并且释放系统资源
        curl_close($ch);
        if (empty($result))
            return $res;
        else
            return $result;

    }

    /**
     * 获取缓存键值
     * @param string $name 缓存Key
     * @return string
     */
    protected function getCacheKey($name)
    {
        return sprintf('%s_%s_%s_%s',
            $this->cachePrefix,
            $this->org_name,
            $this->app_name,
            $name
        );
    }

    /**
     * 缓存数据
     * @param string $name 缓存Key
     * @param mixed $value 缓存Value
     * @param int $duration 缓存有效时间
     * @return bool
     */
    protected function setCache($name, $value, $duration)
    {
        return $this->cache->set($this->getCacheKey($name), $value, $duration);
    }

    /**
     * 获取缓存数据
     * @param $name 缓存Key
     * @return mixed
     */
    protected function getCache($name)
    {
        return $this->cache->get($this->getCacheKey($name));
    }

}
