<?php
/**
 *  @link http://github.com/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @author 57620133@qq.com
 */

namespace console\controllers;

use common\models\Bankcard;
use common\models\Bubble;
use common\models\BubbleLog;
use common\models\GroupMember;
use common\models\Honor;
use common\models\Hotkey;
use common\models\Machine;
use common\models\MarketPrice;
use common\models\Member;
use common\models\MemberMachine;
use common\models\Message;
use common\models\MessageTemplate;
use common\models\Order;
use common\models\RevenueLog;
use common\models\Seller;
use common\models\StarProductLog;
use common\models\TimelinePost;
use common\models\Trade;
use common\models\WheelLog;
use yii;
use yii\console\Controller;

/*
 * 每天0点执行
 * 0 0 * * * /usr/bin/php /home/wwwroot/zantoto/yii night
 * /usr/bin/php /home/wwwroot/zantoto/yii night
 *
 */

class NightController extends Controller
{
    public function init()
    {
        Yii::$app->getUrlManager()->setBaseUrl('/backend/web/index.php');
        Yii::$app->getUrlManager()->setHostInfo('http://cc.cn');
        Yii::$app->getUrlManager()->setScriptUrl('/backend/web/index.php');
    }

    public function actionIndex()
    {
        $time = microtime(true);
        set_time_limit(0);
        if (!ini_set('memory_limit', '-1')) {
            Yii::error("ini_set(memory_limit) error");
            Yii::$app->end();
        }

        $beforeYesterday = date("Y-m-d", strtotime("-2 days"));
        $yesterday = date("Y-m-d", strtotime("-1 day"));
        $today = date("Y-m-d");
        $tomorrow = date("Y-m-d", strtotime("+1 day"));

        Yii::info("###########" . __CLASS__ . " BEGIN");

        if (date('N') == 1) // 每周一
        {
            Yii::info("Begin Weekly ...");
            Yii::info("End Weekly ...");
        }

        if (date('j') == 1) {
            Yii::info("Begin Monthly ...");
            Yii::info("End Monthly ...");
        }

        // 每月10日结算
        //if (date('j') == 10)
        {
            Yii::info("Clear revenue 10 day every month ...");
            RevenueLog::clear();
            Yii::info("End clear ...");
        }

        // 订单取消状态超过90天且无申诉的就删除
        $seconds = 90 * 24 * 3600;
        $query = Order::find()
            ->andWhere(['<', 'updated_at', date('Y-m-d H:i:s', time() - $seconds)])
            ->andWhere(['status' => Order::STATUS_CLOSED, 'has_refund' => 0]);
        foreach ($query->each() as $model) {
            $model->delete();
        }

        Yii::info("###########" . __CLASS__ . " END, (time: " . sprintf('%.3f', microtime(true) - $time) . "s)");
    }
}

