<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\OrderSku;
use common\models\OrderSkuRefund;
use common\models\OrderSkuRefundSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class OrderSkuRefundController
 * @package rest\modules\v1\controllers
 *
 */
class OrderSkuRefundController extends ActiveController
{
    public $modelClass = 'common\models\OrderSkuRefund';

    public $searchModelClass = 'common\models\OrderSkuRefundSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view']);

        return $actions;
    }

    /**
     * 我作为买家的退货列表
     * @param $action
     * @return ActiveDataProvider
     */
    public function prepareDataProvider($action)
    {
        $searchModel = new OrderSkuRefundSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['buyer_id' => Yii::$app->user->id]);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionCreate()
    {
        $model = new OrderSkuRefund();
        $order_sku_id = Yii::$app->request->post('order_sku_id');
        if (empty($order_sku_id)) {
            throw new NotFoundHttpException('参数不能为空');
        }
        $orderSku = OrderSku::findOne(['id' => $order_sku_id]);
        if ($orderSku === null) {
            throw new NotFoundHttpException('无效的参数');
        }
        if ($orderSku->buyer_id != Yii::$app->user->id) {
            throw new NotFoundHttpException('只能操作自己的订单');
        }
        $model->load(Yii::$app->request->post(), '');
        $model->buyer_id = Yii::$app->user->id;
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->getErrors()]);
        }
        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->member_id != Yii::$app->user->id || $model->buyer_id != Yii::$app->user->id ) {
            throw new ForbiddenHttpException('只有卖家才能操作!');
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
            throw new ForbiddenHttpException('just can update you own post-complain!');
        }
        $model->delete();
        return $model;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = OrderSkuRefund::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    public function optional()
    {
        return [
            'index',
            'view',
        ];
    }
}