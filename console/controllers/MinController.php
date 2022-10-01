<?php
/**
 *  @link http://github.com/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @author 57620133@qq.com
 */

// 每5分钟执行一次本脚本
//  * * * * * /usr/bin/php /mnt/wwwroot/zantoto/yii queue/run
//  */5 * * * *  /usr/bin/php /home/wwwroot/zantoto/yii min
//  php yii min
namespace console\controllers;

use common\models\Order;
use Yii;
use yii\console\Controller;


class MinController extends Controller
{
    public function actionIndex()
    {
        set_time_limit(2 * 3600);  //in seconds
        if (!\Yii::$app->mutex->acquire(__METHOD__, 0)) { // 如果get不到锁立即退出脚本
            Yii::error('mutex acquire failed.');
            Yii::$app->end();
        }

        if (!ini_set('memory_limit', '-1')) {
            Yii::error('memory_limit failed.');
            Yii::$app->end();
        }

        $time = microtime(true);
        Yii::warning("###########" . __CLASS__ . " BEGIN");

        // 买家拍下后超过60min不付款, 自动取消, 取消超过30天就在night中删除
        $seconds = 60 * Yii::$app->ks->get('order.max_wait_pay_minutes', 120);
        $query = Order::find()
            ->andWhere(['<', 'created_at', date('Y-m-d H:i:s', time() - $seconds)])
            ->andWhere(['status' => Order::STATUS_AUCTION]);
        foreach ($query->each() as $model) {
            $model->status = Order::STATUS_CLOSED;
            $model->memo = '超时自动取消';
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
            }
        }

        // run一次queue, 执行任务, 主要是执行耗时的任务
        Yii::$app->queue->run(false);

        Yii::warning("###########" . __CLASS__ . " END, (time: " . sprintf('%.3f', microtime(true) - $time) . "s)");

        \Yii::$app->mutex->release(__METHOD__); // release锁
    }

}

