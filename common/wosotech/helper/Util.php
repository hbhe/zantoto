<?php
/**
 * @author hbhe 57620133@qq.com
 * @version 0.01
 */

namespace common\wosotech\helper;

use common\models\AreaCode;
use common\models\User;
use common\models\WxGh;
use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * Class Util
 * @package common\wosotech
 */
class Util
{
    const RANDOM_DIGITS = 'digits';
    const RANDOM_NONCESTRING = 'noncestr';

    /**
     * @param string $obj
     * @param string $log_file
     */
    public static function W($obj = "", $log_file = '')
    {
        if (is_array($obj))
            $str = print_r($obj, true);
        else if (is_object($obj))
            $str = print_r($obj, true);
        else
            $str = "{$obj}";

        if (empty($log_file))
            $log_file = \Yii::$app->getRuntimePath() . '/errors.log';

        $date = date("Y-m-d H:i:s");
        $log_str = sprintf("%s,%s\n", $date, $str);
        error_log($log_str, 3, $log_file);
    }

    /**
     * @param $url
     * @param array $get
     * @param array $post
     * @param string $format
     * @return mixed
     */
    public static function C($url, $get = [], $post = [], $format = 'json')
    {
        $requestUrl = $url . "?";
        foreach ($get as $k => $v) {
            $requestUrl .= "$k=" . urlencode($v) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);
        return Util::curl($requestUrl, $post);
    }

    /**
     * @param $url
     * @param array $posts
     * @param string $format
     * @return mixed
     * @throws \Exception
     */
    public static function curl($url, $posts = [], $format = 'json')
    {
        $response = self::curl_core($url, $posts);
        if ('json' === $format) {
            return json_decode($response, true);
        } else if ('xml' === $format) {
            $respObject = @simplexml_load_string($response);
            if (false !== $respObject)
                return json_decode(json_encode($respObject), true);
            else
                throw new \Exception ('XML error:' . $response);
        }
    }

    /**
     *
     * @param $url
     * @param array $posts
     * @return mixed
     * @throws \Exception
     */
    public static function curl_core($url, $posts = [])
    {
        Yii::info([__METHOD__, __LINE__, $url, $posts]);
        $curlOptions = [
            CURLOPT_HTTPHEADER => array(
                "Contont-Type: text/plain",
            ),
            CURLOPT_USERAGENT => 'WXTPP Client',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            //CURLOPT_POSTFIELDS => is_string($posts) ? $posts : json_encode($posts),
            CURLOPT_POSTFIELDS => $posts,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1,
        ];
        $curlResource = curl_init();
        foreach ($curlOptions as $option => $value) {
            curl_setopt($curlResource, $option, $value);
        }
        $response = curl_exec($curlResource);
        $responseHeaders = curl_getinfo($curlResource);

        // check cURL error
        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);
        curl_close($curlResource);

        if ($errorNumber > 0) {
            throw new \Exception('Curl error requesting "' . $url . '": #' . $errorNumber . ' - ' . $errorMessage);
        }
        if (strncmp($responseHeaders['http_code'], '20', 2) !== 0) {
            throw new \Exception('Request failed with code: ' . $responseHeaders['http_code'] . ', message: ' . $response);
        }
        yii::info($response);
        return $response;
    }

    /**
     * @return string
     * Yii::$app->db->createCommand("ALTER TABLE {{%order_sku_refund}} AUTO_INCREMENT=10000000")->execute();
     */
    public static function generateOid()
    {
        return strtoupper(uniqid()) . sprintf("%03d", rand(0, 999));
    }

    /**
     * U::getWxUserHeadimgurl("http://wx.qlogo.cn/mmopen/17ASicSl2de5EHEpImf7IOxZ5w6MibiaWuzsThDo39s0Lq6U0ZG4Kn04AJDfK4XiaxYicCCpsXH3UxW8goFcPnEkfhv7GO2AeFAtR/0", 64);
     * @param $url
     * @param $size
     * @return string
     */
    public static function getWxUserHeadimgurl($url, $size)
    {
        if (empty($url))
            return $url;
        if (!in_array($size, [0, 46, 64, 96, 132]))
            return $url;
        $pos = strrpos($url, "/");
        $str = substr($url, 0, $pos) . "/$size";
        return $str;
    }

    /**
     * @param $mobile
     * @return bool
     */
    public static function mobileIsValid($mobile)
    {
        $pattern = '/^1\d{10}$/';
        if (preg_match($pattern, $mobile))
            return true;
        return false;
    }

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public static function getYesNoOptionName($key = null)
    {
        $arr = array(
            '1' => '???',
            '0' => '???',
        );
        return $key === null ? $arr : (isset($arr[$key]) ? $arr[$key] : '');
    }

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public static function getNoYesOptionName($key = null)
    {
        $arr = array(
            '0' => '???',
            '1' => '???',
        );
        return $key === null ? $arr : (isset($arr[$key]) ? $arr[$key] : '');
    }

    /**
     * @param string $type
     * @param int $len
     * @return string
     */
    public static function randomString($type = self::RANDOM_DIGITS, $len = 4)
    {
        $code = '';
        switch ($type) {
            case self::RANDOM_DIGITS:
                $chars = '0123456789';
                break;
            case self::RANDOM_NONCESTRING:
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                break;
        }
        $chars_len = strlen($chars);
        while ($len > 0) {
            $code .= substr($chars, rand(0, 10000) % $chars_len, 1);
            $len--;
        }
        return $code;
    }

    /**
     * @param $ptime
     * @return string
     */
    public static function timeago($ptime)
    {
        $ptime = strtotime($ptime);
        $etime = time() - $ptime;
        if ($etime < 1) return '??????';
        $interval = array(
            12 * 30 * 24 * 60 * 60 => '??????' . ' (' . date('Y-m-d', $ptime) . ')',
            30 * 24 * 60 * 60 => '?????????' . ' (' . date('m-d', $ptime) . ')',
            7 * 24 * 60 * 60 => '??????' . ' (' . date('m-d', $ptime) . ')',
            24 * 60 * 60 => '??????',
            60 * 60 => '?????????',
            60 => '?????????',
            1 => '??????'
        );
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        };
    }

    /**
     * @return mixed|null
     */
    static public function getSessionGhsid()
    {
        return empty($gh = self::getSessionGh()) ? null : $gh->sid;
    }

    /**
     * @return null|WxGh
     */
    static public function getSessionGh()
    {
        if (empty($gh_id = self::getSessionGhid())) {
            return null;
        }
        return \common\models\WxGh::findOne(['gh_id' => $gh_id]);
    }

    /**
     * @return array|mixed
     */
    static public function getSessionGhid()
    {
        if (Yii::$app->request->isConsoleRequest) {
            return WxGh::getDefaultGhId();
        }

        if (!empty($gh_id = \Yii::$app->request->get('gh_id'))) {
            Yii::$app->session->set('gh_id', $gh_id);
            return $gh_id;
        } else if (!empty($gh_sid = \Yii::$app->request->get('gh_sid'))) {
            $gh = WxGh::find()->where(['or', ['sid' => $gh_sid], ['appId' => $gh_sid]])->one();
            Yii::$app->session->set('gh_id', $gh->gh_id);
            return $gh->gh_id;
        } else if (isset($_SERVER['gh_id'])) {
            Yii::$app->session->set('gh_id', $_SERVER['gh_id']);
            return $_SERVER['gh_id'];
        } else if (isset($_SERVER['gh_sid'])) {
            $gh = WxGh::find()->where(['or', ['sid' => $_SERVER['gh_sid']], ['appId' => $_SERVER['gh_sid']]])->one();
            Yii::$app->session->set('gh_id', $gh->gh_id);
            return $gh->gh_id;
        } else if (!empty($gh_id = \Yii::$app->session->get('gh_id'))) {
            return $gh_id;
        } else if (!empty($gh_sid = \Yii::$app->session->get('gh_sid'))) {
            $gh = \common\models\WxGh::findOne(['sid' => $gh_sid]);
            return $gh->gh_id;
        } else if (!empty(\Yii::$app->user->identity->gh_id)) {
            return \Yii::$app->user->identity->gh_id;
        } else {
            return WxGh::getDefaultGhId();
        }
    }

    /**
     * @param bool $dynamicOauthCallback
     * @param string $scope
     * @return array|mixed
     */
    static public function getSessionOpenidInfo($dynamicOauthCallback = true, $scope = 'snsapi_base')
    {
        if (YII_ENV_DEV) {
            $model = \common\models\WxUser::findOne(['gh_id' => self::getSessionGhid()]);
            return $model->toArray();
        }

        if (Yii::$app->request->isConsoleRequest) {
        }

        if (!empty(\Yii::$app->session->get('openidInfo'))) {
            return \Yii::$app->session->get('openidInfo');
        } else {
            $gh = Util::getSessionGh();
            $openidInfo = $gh->getSessionOpenid($dynamicOauthCallback, $scope);
            \Yii::$app->session['openidInfo'] = $openidInfo;
            return $openidInfo;
        }
    }

    /**
     * @return array|int|mixed
     */
    static public function getSessionUserId()
    {
        if (!empty(\Yii::$app->request->get('myUserId'))) {
            return \Yii::$app->request->get('myUserId');
        } else if (!empty(\Yii::$app->session->get('myUserId'))) {
            return \Yii::$app->session->get('myUserId');
        } else {
            $model = \common\models\WxUser::findOne(['openid' => self::getSessionOpenid()]);
            \Yii::$app->session['myUserId'] = $model->id;
            return $model->id;
        }
    }

    /**
     * @param bool $dynamicOauthCallback
     * @param string $scope
     * @return array|mixed|string
     */
    static public function getSessionOpenid($dynamicOauthCallback = true, $scope = 'snsapi_base')
    {
        if (YII_ENV_DEV) {
            $model = \common\models\WxUser::findOne(['gh_id' => self::getSessionGhid()]);
            return $model->openid;
        }
        if (Yii::$app->request->isConsoleRequest) {
        }

        if (!empty(\Yii::$app->request->get('openid'))) {
            return \Yii::$app->request->get('openid');
        } else if (!empty(\Yii::$app->session->get('openid'))) {
            $info = \Yii::$app->session->get('openid');
            return $info['openid'];
        } else {
            $gh = \common\models\WxGh::findOne(['gh_id' => self::getSessionGhid()]);
            $info = $gh->getSessionOpenid($dynamicOauthCallback, $scope);
            \Yii::$app->session['openid'] = $info;
            return $info['openid'];
        }
    }

    /**
     * @param int $probability , 400000 -> 40%
     * @return bool
     */
    public static function haveProbability($probability = 400000)
    {
        return mt_rand(0, 1000000) < $probability;
    }

    /**
     * qqface_convert_html("/:moon/:moon");
     * Util::qqface_convert_html(emoji_unified_to_html(emoji_softbank_to_unified($model->content)));
     * @param $text
     * @return mixed
     */
    public static function qqface_convert_html($text)
    {
        $GLOBALS['qqface_maps'] = array("/::)", "/::~", "/::B", "/::|", "/:8-)", "/::<", "/::$", "/::X", "/::Z", "/::'(", "/::-|", "/::@", "/::P", "/::D", "/::O", "/::(", "/::+", "/:--b", "/::Q", "/::T", "/:,@P", "/:,@-D", "/::d", "/:,@o", "/::g", "/:|-)", "/::!", "/::L", "/::>", "/::,@", "/:,@f", "/::-S", "/:?", "/:,@x", "/:,@@", "/::8", "/:,@!", "/:!!!", "/:xx", "/:bye", "/:wipe", "/:dig", "/:handclap", "/:&-(", "/:B-)", "/:<@", "/:@>", "/::-O", "/:>-|", "/:P-(", "/::'|", "/:X-)", "/::*", "/:@x", "/:8*", "/:pd", "/:<W>", "/:beer", "/:basketb", "/:oo", "/:coffee", "/:eat", "/:pig", "/:rose", "/:fade", "/:showlove", "/:heart", "/:break", "/:cake", "/:li", "/:bome", "/:kn", "/:footb", "/:ladybug", "/:shit", "/:moon", "/:sun", "/:gift", "/:hug", "/:strong", "/:weak", "/:share", "/:v", "/:@)", "/:jj", "/:@@", "/:bad", "/:lvu", "/:no", "/:ok", "/:love", "/:<L>", "/:jump", "/:shake", "/:<O>", "/:circle", "/:kotow", "/:turn", "/:skip", "/:oY");
        return str_replace($GLOBALS['qqface_maps'],
            array_map(array('self', 'add_img_label'), array_keys($GLOBALS['qqface_maps'])),
            htmlspecialchars_decode($text, ENT_QUOTES)
        );
    }

    /**
     * @param $v
     * @return string
     */
    public static function add_img_label($v)
    {
        return '<img src="https://res.wx.qq.com/mpres/htmledition/images/icon/emotion/' . $v . '.gif" width="24" height="24">';
    }

    /**
     * @param $url
     * @param $token
     * @param $xml
     * @return mixed
     */
    public static function forwardWechatXML($url, $token, $xml)
    {
        $url = self::setsign($url, $token, $xml);
        yii::info(['ready to forward', $url, $token, $xml]);
        $response = Util::curl_core($url, $xml);
        yii::info(['forward done', $response]);
        return $response;
    }

    /**
     * @param $url
     * @param $token
     * @return string
     */
    public static function setsign($url, $token)
    {
        if (stripos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $sign = array(
            'timestamp' => time(),
            'nonce' => rand(),
        );
        $signkey = array($token, $sign['timestamp'], $sign['nonce']);
        sort($signkey, SORT_STRING);
        $sign['signature'] = sha1(implode($signkey));
        $url .= http_build_query($sign, '', '&');

        return $url;
    }

    /**
     * $smsGateway = new SmsGateway('hehbhehb@sina.com', 'hehbhehb');
     * @param $mobile
     * @param $message
     * @return bool
     */
    public static function sendMessageToNumber($mobile, $message)
    {
        $resp = Util::sendXinhaiSmVerifyCode($mobile, $message);
        if ($resp === false) {
            return false;
        }
        return true;
    }

    /**
     * @param $str
     * @param $len
     * @param string $suffix
     * @return string
     */
    public static function getShortString($str, $len, $suffix = '...')
    {
        return StringHelper::truncate($str, $len);
    }

    public static function short($string, $length = 20)
    {
        return StringHelper::truncate($string, $length);
    }

    public static function sendVerifycodeAjaxToSuper($params)
    {
        $member = User::getSuperAdmin();
        $params['mobile'] = $member->mobile;
        return self::sendVerifycodeAjax($params);
    }

    public static function sendVerifycodeAjax($params)
    {
        $mobile = ArrayHelper::getValue($params, 'mobile');
        $content = ArrayHelper::getValue($params, 'content', '');
        $template = ArrayHelper::getValue($params, 'template', '');  // SMS_001
        if (empty($mobile)) {
            return Json::encode(['code' => 1, 'msg' => '?????????????????????!']);
        }

        if (!preg_match('/^1\d{10}$/', $mobile)) {
            return Json::encode(['code' => 1, 'msg' => '??????????????????!']);
        }

        $verifyCode = Util::randomString();
        Yii::info("$mobile verifyCode = $verifyCode");
        try {
            $results = Yii::$app->sm->send($mobile, [
                'content' => $content,
                'template' => $template,
                'data' => [
                    'code' => $verifyCode,
                ],
            ]);
        } catch (\Exception $e) {
            Yii::error(['send verify failed', __METHOD__, __LINE__, $e->getExceptions()]);
            return Json::encode(['code' => 1, "msg" => '????????????????????????????????????']);
        }

        Yii::$app->cache->set('SMS-VERIFY-CODE' . $mobile, $verifyCode, YII_DEBUG ? 24 * 3600 : 5 * 60);
        //Yii::info(['set ok', 'SMS-VERIFY-CODE' . $mobile, \Yii::$app->cache->get('SMS-VERIFY-CODE' . $mobile)]);
        return Json::encode(['code' => 0]);
    }

    public static function checkVerifyCode($mobile, $verifyCode)
    {
        $verifyCodeCache = \Yii::$app->cache->get('SMS-VERIFY-CODE' . $mobile);
        //Yii::info(['checkVerifyCode, compare...', __METHOD__, __LINE__, $verifyCodeCache, $verifyCode]);
        //return YII_DEBUG ? true : $verifyCodeCache == $verifyCode;
        return true; // for test
        return YII_ENV_DEV && YII_DEBUG ? true : $verifyCodeCache == $verifyCode;
    }

    public static function getIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /*
        [
            'code' => 0,
            'data' => [
                'ip' => '223.75.1.199',
                'country' => '??????',
                'area' => '',
                'region' => '??????',
                'city' => '??????',
                'county' => 'XX',
                'isp' => '??????',
                'country_id' => 'CN',
                'area_id' => '',
                'region_id' => '420000',
                'city_id' => '420100',
                'county_id' => 'xx',
                'isp_id' => '100025',
            ],
        ],
    */

    /**
     * @param null $ip
     * @return AreaCode
     */
    public static function getCurrentAreaCode($ip = null)
    {
//        $city_id = Yii::$app->request->get('area_id') ?: Yii::$app->session->get('area_id');
//        if (($model = AreaCode::findOne($city_id)) !== null) {
//            Yii::$app->session->set('area_id', $city_id);
//            return $model;
//        }

        if (null === $ip) {
            $ip = YII_ENV_DEV ? '223.75.1.199' : self::getIpAddr();
        }

        $key = [__METHOD__, $ip];
        $arr = Yii::$app->cache->get($key);
        if ($arr === false) {
            $arr = self::C('http://ip.taobao.com/service/getIpInfo.php', ['ip' => $ip]);
            Yii::$app->cache->set($key, $arr, YII_ENV_DEV ? 10 : 30 * 24 * 3600);
        }

        $city_id = ArrayHelper::getValue($arr, 'data.city_id', '0');
        $region_id = ArrayHelper::getValue($arr, 'data.region_id', '0');
        if (($model = AreaCode::findOne($city_id)) !== null) {
            return $model;
        }
        if (($model = AreaCode::findOne(['parent_id' => $region_id])) !== null) {
            return $model;
        }
        Yii::info([__METHOD__, __LINE__, $arr, $model->toArray()]);
        return $model;
    }

    // ?????????????????????????????????, ???????????????????????????, ???????????????
    public static function saveUrl()
    {
        Yii::$app->session->set('previousRoute', Yii::$app->controller->getRoute());
        Yii::$app->session->set('previousParams', Yii::$app->request->get());
    }

    public static function getPreviousUrl(array $params = [], $scheme = false)
    {
        $parentParams = Yii::$app->session->get('previousParams');
        $parentParams[0] = '/' . Yii::$app->session->get('previousRoute');
        $route = ArrayHelper::merge($parentParams, $params);

        return Url::toRoute($route, $scheme);
    }

    public static function sendSmsbao($mobile, $verify_code, $template)
    {

        $arr = [
            'SMS_001' => "???CC??????????????????????????????{$verify_code}???5?????????????????????????????????????????????", // ????????????
            'SMS_002' => "???CC??????????????????{$verify_code}?????????????????????????????????????????????????????????????????????????????????", // ????????????
            'SMS_003' => "???CC??????????????????{$verify_code}????????????????????????????????????????????????????????????", // ????????????
            'SMS_004' => "???CC????????????????????????{$verify_code}????????????????????????????????????????????????????????????????????????", // ???????????????
            'SMS_005' => "???CC??????????????????{$verify_code}??????????????????????????????????????????????????????????????????????????????", // ??????????????????
        ];
        $content = ArrayHelper::getValue($arr, $template, '???CC??????????????????{$verify_code}, ???????????????????????????');
        $statusStr = array(
            "0" => "??????????????????",
            "-1" => "????????????",
            "-2" => "????????????????????????,???????????????curl??????fsocket???????????????????????????????????????????????????",
            "30" => "????????????",
            "40" => "???????????????",
            "41" => "????????????",
            "42" => "???????????????",
            "43" => "IP????????????",
            "50" => "?????????????????????"
        );
        $smsapi = "http://api.smsbao.com/";
        // $user = "ccworld";
        // $pass = md5("ccnewworld2019");
        $user = Yii::$app->params['smsbaoUsername'];
        $pass = md5(Yii::$app->params['smsbaoPassword']);
        $content = ArrayHelper::getValue($arr, $template, "???CC??????????????????{$verify_code}, ???????????????????????????");
        $phone = $mobile;
        $sendurl = $smsapi . "sms?u=" . $user . "&p=" . $pass . "&m=" . $phone . "&c=" . urlencode($content);
        $result = file_get_contents($sendurl);
        if ($result != '0') {
            Yii::error([__METHOD__, __LINE__, $result, $statusStr[$result]]);
            return false;
        }
        Yii::info([__METHOD__, __LINE__, $result, $statusStr[$result]]);
        return true;
    }

    // ?????????????????????????????????, ??????????????????????????????
    public static function sendShudunContent($mobiles, $content)
    {
        $smsapi = "http://118.178.138.170/msg/HttpBatchSendSM";
        $arr = self::C($smsapi, [
            // 'account' => 'tz15157063162',
            // 'pswd' => 'Tz950516',
            'account' => Yii::$app->params['sm_username'],
            'pswd' => Yii::$app->params['sm_password'],
            'msg' => urlencode($content), // ???????????????700?????????
            'mobile' => $mobiles, // ??????5000???mobile, ????????????
            'resptype' => 'json',
        ], []);
        if ($arr['result'] != '0') {
            Yii::error([__METHOD__, __LINE__, $arr,]);
            return false;
        }
        Yii::info([__METHOD__, __LINE__, $arr,]);
        return true;
    }

    /*
    Util::sendShudun('SMS_001', [
        ['15527210477', '1111'],
        ['13871407676', '2222'],
    ]);
    Util::sendShudun('SMS_002', [['15527210477', '3333']]);
    Util::sendShudun('SMS_003', [['15527210477']]);
    Util::sendShudun('SMS_004', [['15527210477']]);
    Util::sendShudun('SMS_005', [['15527210477']]);
    Util::sendShudun('SMS_006', [['15527210477']]);
    Util::sendShudun('SMS_007', [['15527210477']]);
    Util::sendShudun('SMS_008', [['15527210477']]);
    Util::sendShudun('SMS_009', [['15527210477']]);
    Util::sendShudun('SMS_010', [['15527210477']]);
    Util::sendShudun('SMS_011', [['15527210477', '????????????', '???????????? http://baidu.com']]);
    Util::sendShudun('SMS_011', [
        ['15527210477', '????????????1', '???????????? http://baidu.com'],
        ['13871407676', '????????????2', '???????????? http://baidu.com'],
    ]);
    Util::sendShudun('SMS_012', [['13871407676', '??????????????????']]);
    */
    public static function sendShudun($template = 'SMS_001', $groups)
    {
        $arr = [
            'SMS_001' => '??????????????????:{$var}, ?????????????????????',
            'SMS_002' => '??????????????????????????????????????????{$var}',
            'SMS_003' => '???????????????????????????????????????????????????????????????????????????????????????',
            'SMS_004' => '??????????????????????????????????????????????????????????????????',
            'SMS_005' => '?????????????????????????????????????????????????????????????????????????????????????????????',
            'SMS_006' => '??????????????????????????????????????????????????????????????????',
            'SMS_007' => '????????????????????????????????????????????????????????????????????????',
            'SMS_008' => '??????????????????????????????????????????????????????????????????????????????????????????',
            'SMS_009' => '????????????????????????????????????????????????????????????????????????????????????',
            'SMS_010' => '???????????????????????????????????????????????????????????????????????????????????????????????????',
            'SMS_011' => '???????????????????????????????????????????????????{$var}?????????????????????????????????????????????????????????{$var}?????????TD?????????',
            'SMS_012' => '?????????????????????{$var}???????????????????????????????????????????????????????????????????????????',
        ];
        $content = ArrayHelper::getValue($arr, $template);
        $smsapi = "http://118.178.138.170/msg/HttpVarSM";

        $groupArray = [];
        foreach ($groups as $group) {
            $groupArray[] = implode(',', $group);
        }

        if (YII_ENV_DEV) {
            Yii::info([__METHOD__, __LINE__, $smsapi, $template, $content, $groupArray]);
            return true;
        }

        $arr = self::C($smsapi, [
            'account' => Yii::$app->params['sm_username'],
            'pswd' => Yii::$app->params['sm_password'],
            'msg' => urlencode($content),
            'params' => urlencode(implode(';', $groupArray)),
            'resptype' => 'json',
        ], []);
        if ($arr['result'] != '0') {
            Yii::error([__METHOD__, __LINE__, $template, $content, $groupArray, $arr]);
            return false;
        }
        Yii::info([__METHOD__, __LINE__, $template, $content, $groupArray, $arr]);
        return true;
    }

    // $url = "http://m.rki-ccworld.com/register.html?pid=cc{$id}";
    public function getQrCodeImageUrl($url)
    {
        $fileName = md5($url) . '.png';
        $filePath = Yii::getAlias("@storage/web/images/qrcode/{$fileName}");
        if (file_exists($filePath)) {
            return Yii::getAlias("@storageUrl/images/qrcode/{$fileName}");
        }
        require_once Yii::getAlias('@vendor/phpqrcode/phpqrcode.php');
        \QRcode::png($url, $filePath); // save as file, \QRcode::png($url, Yii::getAlias('@runtime/xxx.png'));
        return Yii::getAlias("@storageUrl/images/qrcode/{$fileName}");
    }

    /**
     * ???????????????base64??????
     *
     * @param  string $imgPath ?????????????????????????????????????????????
     * @return string          base64??????????????????
     */
    public static function getBase64Img($imgPath)
    {
        $mimeType = FileHelper::getMimeType(Yii::getAlias($imgPath));
        $fileContent = base64_encode(file_get_contents($filePath));

        return 'data:' . $mimeType . ';base64,' . $fileContent;
    }

    /**
     * getRandomWeightedElement()
     * Utility function for getting random values with weighting.
     * Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
     * An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
     * The return value is the array key, A, B, or C in this case.  Note that the values assigned
     * do not have to be percentages.  The values are simply relative to each other.  If one value
     * weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
     * chance of being selected.  Also note that weights should be integers.
     *
     * @param array $weightedValues
     */
    static public function getRandomWeightedElement(array $weightedValues)
    {
        $rand = mt_rand(1, (int)array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }
    }

    /*
        [
            'chargeStatus' => 1,
            'message' => '??????',
            'data' => [
                'orderNo' => '1545125444463',
                'handleTime' => '2018-12-18 17:30:44',
                'result' => '01',
                'province' => 'xx???',
                'city' => 'xx??????',
                'country' => 'xx???',
                'birthday' => '19800101',
                'age' => '38',
                'gender' => '1',
                'remark' => '??????',
            ],
            'code' => '200000',
        ],

    */
    static public function identityIsTrue($idNum, $name)
    {
        $key = [__METHOD__, $idNum, $name];
        $arr = Yii::$app->cache->get($key);
        if ($arr === false) {
            $appId = 'ivYB2rRi';
            $appKey = 'JZOhGpF7';
            if (1) {
                $url = 'https://api.253.com/open/idcard/id-card-auth';
                $params = [
                    'appId' => $appId,
                    'appKey' => $appKey,
                    'name' => $name,
                    'idNum' => $idNum,
                ];
            } else {
                // safe mode
                $url = 'https://api.253.com/open/idcard/id-card-auth/vs';
                $str = 'appId' . $appId . 'idNum' . $idNum . 'name' . $name;
                $hex = hash_hmac("sha1", $str, $appKey, true);
                $sign = base64_encode($hex);
                $params = [
                    'appId' => $appId,
                    'name' => $name,
                    'idNum' => $idNum,
                    'sign' => $sign,
                ];
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);
            $arr = json_decode($result, true);
            Yii::info([$result, $arr]);
        } else {
            Yii::info('check name from cache');
        }

        if ($arr['code'] != '200000') {
            Yii::error([__METHOD__, __LINE__, $result, $arr]);
            throw new \Exception($arr['message']);
        }

        // ?????????cache
        if ($arr['chargeStatus'] == 1) {
            Yii::info([__METHOD__, __LINE__, 'cache it']);
            Yii::$app->cache->set($key, $arr, 10 * 24 * 3600);
        }

        if ($arr['data']['result'] == '01') {
            return true;
        }
        return false;
    }

    /*
     * $datetime?????????????????????????????????seconds???
     */
    static public function isExpired($datetime, $seconds)
    {
        return $datetime < date('Y-m-d H:i:s', time() - $seconds);
    }

    // ?????????4?????????
    static public function getFloor($number)
    {
        return floor($number * 10000) / 10000;
    }

    /**
     * ???????????????????????????application??????
     * for EasyWechat 4.0
     * @param string $scope
     * @param bool $dynamicOauthCallback
     * @param null $callbackUrl
     * @return \EasyWeChat\OfficialAccount\Application
     * sample:
     * $app = Util::getOfficialAccount();
     * $menu = $app->menu;
     * $responseArray = $menu->current();
     * Yii::info($responseArray);
     */
    static public function getOfficialAccount($scope = 'snsapi_base', $dynamicOauthCallback = true, $callbackUrl = null)
    {
        static $_app;
        $config = [
            'app_id' => APPID, // ???????????????APPID
            'secret' => APPSECRET,
            'token' => 'your-token',
            'aes_key' => '',
            'response_type' => 'array',
            'log' => [
                'default' => 'dev',
                'channels' => [
                    'dev' => [
                        'driver' => 'single',
                        //'path' => 'easywechat.log',
                        'path' => Yii::getAlias('@runtime/logs/easywechat.log'),
                        'level' => 'debug',
                    ],
                    'prod' => [
                        'driver' => 'daily',
                        'path' => Yii::getAlias('@runtime/logs/easywechat.log'),
                        'level' => 'info',
                    ],
                ],
            ],

            'http' => [
                'max_retries' => 1,
                'retry_delay' => 500,
                'timeout' => 5.0,
            ],

            'oauth' => [
                'scopes' => [$scope], // ???????????????snsapi_userinfo / snsapi_base?????????????????????snsapi_login
                'callback' => $dynamicOauthCallback && (!\yii::$app instanceof \yii\console\Application) ? Url::current() : $callbackUrl,        //Url::to(['wap/callback'])
            ],
        ];

        if (!$_app) {
            $_app = \EasyWeChat\Factory::officialAccount($config);
        }

        return $_app;
    }

    /**
     * ?????????????????????????????????????????????ID(?????????OPENTM207791155???ID)??????????????????????????????, ????????????ID?????????, ?????????????????????????????????
     * @param $app
     * @param $openid
     * @param $url
     *
     * Util::sendTemplateSample(Util::getOfficialAccount(), 'oEvDz1NUM6iRkczLe-aFxehOLzTw', 'http://baidu.com');
     */
    static public function sendTemplateSample($app, $openid, $url)
    {
        $url = '';
        $first = '';
        $remark = PHP_EOL;
        $first .= '??????????????????????????????????????????' . PHP_EOL;
        $remark .= '????????????' . $openid . '????????????????????????';
        $data = [
            'first' => [
                'value' => $first,
                'color' => '#173177',
            ],
            'keyword1' => [
                'value' => 'hello',
                'color' => '#173177',
            ],
            'keyword2' => [
                'value' => 'world',
                'color' => '#173177',
            ],
            'remark' => [
                'value' => $remark,
                'color' => '#173177',
            ],
        ];

        $responseArray = $app->template_message->send([
            'touser' => $openid,
            'template_id' => 'private_template_id', // ???????????????????????????ID, ?????????OPENTM207791155?????????????????????ID
            'url' => $url,
            'topcolor' => '',
            'data' => $data,
        ]);
    }

    /**
     * ????????????
     * @param $input
     * @return array
     */
    /*
            $arr = array(
                'arm' => array('A', 'B', 'C'),
                'gender' => array('Female', 'Male'),
            );
            \Yii::info($arr);
            [
                [
                    'arm' => 'A',
                    'gender' => 'Female',
                ],
                [
                    'arm' => 'A',
                    'gender' => 'Male',
                ],
                [
                    'arm' => 'B',
                    'gender' => 'Female',
                ],
                [
                    'arm' => 'B',
                    'gender' => 'Male',
                ],
                [
                    'arm' => 'C',
                    'gender' => 'Female',
                ],
                [
                    'arm' => 'C',
                    'gender' => 'Male',
                ],
            ]
    */
    static public function cartesian($input)
    {
        $result = array(array());

        foreach ($input as $key => $values) {
            $append = array();

            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }

    public static function generateSid() : string
    {
        $id = 'D' . date('YmdHis') . sprintf("%03d", rand(0, 999));
        return Yii::$app->request->isConsoleRequest ? ('D' . microtime(true) . rand(0, 999)) : $id;
    }

    /**
     * @return string
     * ?????????: ??????+??????
     */
    public static function generateSequence()
    {
        if (!\Yii::$app->mutex->acquire(__METHOD__, 3600)) {
            Yii::error([__METHOD__, __LINE__, 'mutex acquire failed.']);
            Yii::$app->end();
        }

        $key = 'OID-' . date('Ymd');
        $key_yesterday = 'OID-' . date("Ymd", strtotime("-1 day"));
        $id = Yii::$app->ks->get($key, 1, false);
        if ($id == 1) {
            Yii::info('try to remove yesterday oid');
            Yii::$app->ks->remove($key_yesterday);
        }
        Yii::$app->ks->set($key, $id + 1);
        $oid = 'D' . date('Ymd') . sprintf("%04d", $id);

        \Yii::$app->mutex->release(__METHOD__);
        return $oid;
    }

    /**
     * @param $file
     * 1. ??????command???????????????ffmepg
     * C:\ffmpeg-win64\bin\ffmpeg.exe -i D:\htdocs\zantoto\test.mp4 -y -f image2 -ss 2 -s 600x400 D:\htdocs\zantoto\a.jpg
     *
     * 2. ?????????PHP?????????php-ffmpeg;
     * ??????????????????????????????ffmpge???ffprobe????????????, ??????create()???????????????????????????, ??????????????????????????????PATH???C:\ffmpeg-win64\bin
     */
    static public function generateVideoPoster($file, $second = 3)
    {
        $output_file = "{$file}.jpg";

        $ffmpeg = \FFMpeg\FFMpeg::create([
            'ffmpeg.binaries' => YII_ENV_DEV ? 'C:\ffmpeg\bin\ffmpeg.exe' : '/usr/bin/ffmpeg',
            'ffprobe.binaries' => YII_ENV_DEV ? 'C:\ffmpeg\bin\ffprobe.exe' : '/usr/bin/ffprobe',
        ]);
        $video = $ffmpeg->open($file);
        $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($second));
        $frame->save($output_file);
        //$a = Yii::$app->imageresize->generateImage($output_file, 200, 200);
        return;

        $cmd = "C:\\ffmpeg-win64\\bin\\ffmpeg.exe -i {$file} -y -f image2 -ss {$second} -frames 1 -s 300x200 {$output_file}"; // -s 352x240
        exec($cmd, $output, $retval);
        if ($retval != 0) {
            Yii::error(['??????????????????', __METHOD__, __LINE__, $cmd, $output, $retval]);
            return;
        }
        Yii::info([__METHOD__, __LINE__, $cmd, $output, $retval]);

        return;
    }

    /**
     * a.txt -> .txt
     * @param $filename
     * @return string
     */
    static public function getFilenameExt($filename)
    {
        return substr($filename, strrpos($filename, '.', -1));
    }
}

/*
    public static function sendShudunOld($mobile, $verify_code, $template = 'SMS_001') {
        $arr = [
            'SMS_001' => '??????????????????:{$var}, ?????????????????????',
            'SMS_002' => '??????????????????????????????{$var}',
            'SMS_003' => '???????????????????????????????????????????????????????????????????????????????????????',
            'SMS_004' => '??????????????????????????????????????????????????????????????????',
            'SMS_005' => '?????????????????????????????????????????????????????????????????????????????????????????????',
            'SMS_006' => '??????????????????????????????????????????????????????????????????',
            'SMS_007' => '????????????????????????????????????????????????????????????????????????',
            'SMS_008' => '??????????????????????????????????????????????????????????????????????????????????????????',
            'SMS_009' => '????????????????????????????????????????????????????????????????????????????????????',
            'SMS_010' => '???????????????????????????????????????????????????????????????????????????????????????????????????',
            'SMS_011' => '???????????????????????????????????????????????????{$var}?????????????????????????????????????????????????????????{$var}?????????TD?????????',
            'SMS_012' => '?????????????????????{$var}???????????????????????????????????????????????????????????????????????????',
        ];
        $content = ArrayHelper::getValue($arr, $template);
        $smsapi = "http://118.178.138.170/msg/HttpVarSM";

        $arr = self::C($smsapi, [
            'account' => Yii::$app->params['sm_username'],
            'pswd' => Yii::$app->params['sm_password'],
            'msg' => urlencode($content),
            //'params' => implode(',', [$mobile, $verify_code]),
            'params' => urlencode(implode(',', [$mobile, $verify_code])),
            'resptype' => 'json',
        ], []);
        if ($arr['result'] != '0') {
            Yii::error([__METHOD__, __LINE__, $arr, ]);
            return false;
        }
        Yii::info([__METHOD__, __LINE__, $arr, ]);
        return true;
    }
*/

