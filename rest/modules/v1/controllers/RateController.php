<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Order;
use common\models\OrderSku;
use common\models\Rate;
use common\models\RateSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class RateController
 * @package rest\modules\v1\controllers
 *
 */
class RateController extends ActiveController
{
    public $modelClass = 'common\models\Rate';

    public $searchModelClass = 'common\models\RateSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view']);

        return $actions;
    }

    /**
     * @param $action
     * @return ActiveDataProvider
     */
    public function prepareDataProvider($action)
    {
        if (empty(Yii::$app->request->get('product_id'))) {
            throw new ForbiddenHttpException('Invalid product_id!');
        }
        $searchModel = new RateSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['is_hidden' => 0]);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionMine()
    {
        $searchModel = new RateSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['or', ['member_id' => Yii::$app->user->id], ['buyer_id' => Yii::$app->user->id]]);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionCreate()
    {
        $order_sku_id = Yii::$app->request->post('order_sku_id');
        if (empty($order_sku_id)) {
            throw new HttpException(400, '无效的参数');
        }

        $rate = Rate::findOne(['order_sku_id' => $order_sku_id]);
        if ($rate !== null) {
            throw new HttpException(400, '请勿重复评价');
        }

        $orderSku = OrderSku::findOne(['id' => $order_sku_id]);
        if ($orderSku === null) {
            throw new HttpException(400, '此订单不存在');
        }

        if ($orderSku->buyer_id != Yii::$app->user->id) {
            throw new HttpException(400, '只有买家才可以评价');
        }

        if ($orderSku->order->status != Order::STATUS_CONFIRM) {
            throw new HttpException(400, '确认收货后才可以进行评价');
        }

        $model = new Rate();
        $model->load(Yii::$app->request->post(), '');
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
            throw new HttpException(400, '评价失败');
        }

        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->buyer_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own item!');
        }
        $model->load(Yii::$app->request->post(), '');
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->buyer_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own item!');
        }
        $model->delete();
        return $model;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->buyer_id != Yii::$app->user->id && $model->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own item!');
        }
        return $model;
    }

    public function findModel($id)
    {
        if (is_numeric($id)) {
            $model = Rate::find()->where(['order_sku_id' => (int)$id])->one();
        } else {
            $model = Rate::find()->where(['id' => $id])->one();
        }
        if ($model === null) {
            throw new NotFoundHttpException("The item ($id) does not exist.");
        }
        return $model;
    }

    public function optional()
    {
        return [
            'index',
            'view',
        ];
    }
}