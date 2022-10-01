<?php
/**
 *  @link http://github.com/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @author 57620133@qq.com
 */

//  * * * * * /usr/bin/php /home/wwwroot/zantoto/console/yii queue/run
//  */20 * * * *  /usr/bin/php /home/wwwroot/zantoto/console/yii min-twenty
//  php yii min-twenty
namespace console\controllers;

use common\models\Order;
use common\models\Rate;
use Yii;
use yii\console\Controller;


class MinTwentyController extends Controller
{
    public function actionIndex()
    {
        set_time_limit(3600);  //in seconds
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

        // 卖家发货后,买家超过15天不确认, 自动确认收货
        $seconds = 24 * 3600 * Yii::$app->ks->get('order.max_wait_confirm_receive', 15);
        $query = Order::find()
            ->andWhere(['<', 'shipping_time', date('Y-m-d H:i:s', time() - $seconds)])
            ->andWhere(['status' => Order::STATUS_SHIPPED]);
        foreach ($query->each() as $model) {
            $model->status = Order::STATUS_CONFIRM;
            $model->confirm_time = date("Y-m-d H:i:s");
            $model->memo = '超时自动确认收货';
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
            }
        }

        // 确认收货后, 超过xx天不评价, 自动评价
        $seconds = 24 * 3600 * Yii::$app->ks->get('order.max_wait_rate', 7);
        $query = Order::find()
            ->andWhere(['<', 'confirm_time', date('Y-m-d H:i:s', time() - $seconds)])
            ->andWhere(['status' => Order::STATUS_CONFIRM]);
        foreach ($query->each() as $model) {
            $model->status = Order::STATUS_RATED;
            $model->rate_time = date("Y-m-d H:i:s");
            $model->memo = '超时自动评价';
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
            }

            // 对订单中的sku进行评价
            foreach ($model->orderSkus as $orderSku) {
                if (Rate::findOne(['order_sku_id' => $orderSku->id])) {
                    Yii::error([__METHOD__, __LINE__, 'rate repeat', $orderSku->id]);
                    continue;
                }
                $rate = new Rate();
                $rate->attributes = [
                    'order_sku_id' => $orderSku->id,
                    'is_auto' => 1,
                    'score' => 3,
                    'content' => '默认好评!',
                ];
                if (!$rate->save()) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                }
            }

        }

        Yii::warning("###########" . __CLASS__ . " END, (time: " . sprintf('%.3f', microtime(true) - $time) . "s)");

        \Yii::$app->mutex->release(__METHOD__); // release锁
    }

}


