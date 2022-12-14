<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Cart;
use common\models\Member;
use common\models\MemberAddress;
use common\models\Order;
use common\models\OrderSearch;
use common\models\OrderSku;
use common\models\Sku;
use rest\controllers\ActiveController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class OrderController
 * @package rest\modules\v1\controllers
 *
 */
class OrderController extends ActiveController
{
    public $modelClass = 'common\models\Order';

    public $searchModelClass = 'common\models\OrderSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view']);

        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $searchModel = new OrderSearch;
        $params = Yii::$app->request->queryParams;
        if (ArrayHelper::getValue($params, 'status', '') != '') {
            $searchModel->status = explode(',', $params['status']);
            unset($params['status']);
        }
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['buyer_id' => Yii::$app->user->id]);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionSellerOrders()
    {
        $searchModel = new OrderSearch;
        $params = Yii::$app->request->queryParams;
        if (ArrayHelper::getValue($params, 'status', '') != '') {
            $searchModel->status = explode(',', $params['status']);
            unset($params['status']);
        }
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['member_id' => Yii::$app->user->id]);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function actionUnbalance()
    {
        throw new HttpException(400, 'not support!');

        $searchModel = new OrderSearch;
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['buyer_id' => Yii::$app->user->id]);

        return $dataProvider;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = Order::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    /**
     * ????????????????????????????????????????????????, ?????????????????????????????????
     * @return array
     * @throws HttpException
     * @throws \Exception
     */
    public function actionCreate()
    {
        $order_skus = Yii::$app->request->post('order_skus');
        if (empty($order_skus)) {
            throw new HttpException(400, '??????SKU????????????');
        }
        if (Yii::$app->request->post('pay_method') === null) {
            throw new HttpException(400, '????????????????????????');
        }
        $member_address_id = Yii::$app->request->post('member_address_id');
        $memberAddress = MemberAddress::findOne(['id' => $member_address_id]);
        if (empty($member_address_id) || $memberAddress === null) {
            throw new HttpException(400, '????????????????????????');
        }

        // ?????????????????????, ???$order_skus????????????
        $new_order_skus = [];
        $total_accept_coupon_amount = 0;
        foreach ($order_skus as $order_sku) {
            $sku = Sku::findOne(['id' => $order_sku['sku_id']]);
            if ($sku === null) {
                throw new HttpException(400, '?????????SKU');
            }
            if ($sku->quantity < $order_sku['quantity']) {
                throw new HttpException(400, "SKU({$sku->sku_code})????????????({$sku->quantity})??????{$order_sku['quantity']}");
            }
            $new_order_skus[$sku->member_id][] = $order_sku;
        }
        $tid = uniqid();
        $orders = [];
        $coupon_used_rest = $coupon_used;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($new_order_skus as $member_id => $order_skus) {
                $model = new Order();
                $model->load(Yii::$app->request->post(), '');
                $model->tid = $tid;
                $model->member_id = $member_id;
                $model->order_skus = $order_skus;
                $model->buyer_id = Yii::$app->user->id;
                $model->status = Order::STATUS_AUCTION;
                $model->member_address_id = $member_address_id;
                if (!$model->save()) {
                    Yii::error([__METHOD__, __LINE__, $model->getErrors()]);
                    throw new HttpException(400, '????????????');
                }
                $total_amount = $quantity = $revenue_amount = $award_fish = $award_coupon = 0;
                foreach ($model->order_skus as $order_sku) {
                    $ar = new OrderSku();
                    $ar->load($order_sku, '');
                    $ar->member_id = $member_id;
                    $ar->buyer_id = Yii::$app->user->id;
                    $ar->order_id = $model->id;
                    if ($member_id == Member::ROOT_ID) { // ?????????????????????
                        $ar->coupon_used = $coupon_used_rest > $ar->quantity * $ar->sku->getAcceptCouponAmount() ? $ar->quantity * $ar->sku->getAcceptCouponAmount() : $coupon_used_rest;
                        $coupon_used_rest -= $ar->coupon_used;
                    }
                    if (!$ar->save()) {
                        Yii::error([__METHOD__, __LINE__, $ar->getErrors()]);
                        throw new HttpException(400, '????????????');
                    }
                    $total_amount += $ar->amount;
                    $quantity += $ar->quantity;
                }
                $model->total_amount = $total_amount;
                $model->pay_amount = $total_amount;
                $model->quantity = $quantity;
                if (!$model->save()) {
                    Yii::error([__METHOD__, __LINE__, $model->getErrors()]);
                    throw new HttpException(400, '????????????');
                }
                $orders[] = $model;
            }

            // ??????????????????????????????
            $sku_ids = ArrayHelper::getColumn($order_skus, 'sku_id');
            Cart::deleteAll(['sku_id' => $sku_ids]);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $orders;
    }

    public function actionUpdate($id)
    {
        throw new ForbiddenHttpException('not supported!');

        $model = $this->findModel($id);
        if ($model->buyer_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own order!');
        }
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $model;
    }

    /**
     * ??????????????????
     * @param $id
     * @return array|Order|null
     * @throws ForbiddenHttpException
     */
    public function actionConfirmReceive()
    {
        $ids = Yii::$app->request->post('order_ids');
        if (empty($ids)) {
            throw new ForbiddenHttpException('???????????????');
        }
        $models = Order::findAll(['id' => $ids]);
        foreach ($models as $model) {
            if ($model->buyer_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException('???????????????????????????');
            }
            $model->status = Order::STATUS_CONFIRM;
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
            }
        }
        return $models;
    }

    /**
     * ??????????????????
     * @param $id
     * @return array|Order|null
     * @throws ForbiddenHttpException
     */
    public function actionConfirmShip()
    {
        $order_id = Yii::$app->request->post('order_id');
        $express_company = Yii::$app->request->post('express_company');
        $express_code = Yii::$app->request->post('express_code');
        if (empty($order_id)) {
            throw new ForbiddenHttpException('??????ID????????????');
        }
        if (empty($express_company) || empty($express_code)) {
            throw new ForbiddenHttpException('?????????????????????????????????');
        }

        $model = Order::findOne(['id' => $order_id]);
        if ($model === null) {
            throw new ForbiddenHttpException('???????????????');
        }
        if ($model->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('???????????????????????????');
        }
        $model->express_company = $express_company;
        $model->express_code = $express_code;
        $model->status = Order::STATUS_SHIPPED;
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }

        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->buyer_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can delete you own item!');
        }
        // $seconds = 60 * Yii::$app->ks->get('order.max_wait_pay_minutes', 120);
        if ($model->status != Order::STATUS_CLOSED || !empty($model->pay_time)) {
            throw new ForbiddenHttpException('?????????????????????????????????????????????');
        }
        $model->delete();
        return $model;
    }

    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        if ($model->buyer_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can cancel you own order!');
        }
        $model->status = Order::STATUS_CLOSED;
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $model;
    }

    /**
     * ??????????????????????????????
     * @return static[]
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionPayOrdersByRevenue()
    {
        $order_ids = Yii::$app->request->post('order_ids');
        $buyer = Yii::$app->user->identity;
        if (empty($order_ids)) {
            throw new ForbiddenHttpException('??????ID????????????');
        }
        $models = Order::findAll(['id' => $order_ids]);
        $amount = 0;
        foreach ($models as $model) {
            if ($model->buyer_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException('???????????????????????????');
            }
            if ($model->status != Order::STATUS_AUCTION) {
                throw new ForbiddenHttpException('??????????????????????????????');
            }
            $amount += $model->pay_amount;
        }

        if ($buyer->balance_revenue < $amount) {
            throw new ForbiddenHttpException('????????????');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {

            foreach ($models as $model) {
                $model->payOrderByRevenue();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $models;
    }

    public function optional()
    {
        return [
//            'index',
//            'view',
        ];
    }

}