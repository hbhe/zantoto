<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Cart;
use common\models\CartSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class CartController
 * @package rest\modules\v1\controllers
 *
 */
class CartController extends ActiveController
{
    public $modelClass = 'common\models\Cart';

    public $searchModelClass = 'common\models\CartSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    /**
     * @param $action
     * @return ActiveDataProvider
     */
    public function prepareDataProvider($action)
    {
        $searchModel = new CartSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->joinWith(['product', 'sku']);
        $dataProvider->query->andWhere(['buyer_id' => Yii::$app->user->id]);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionCreate()
    {
        $sku_code = Yii::$app->request->post('sku_code');
        if (empty($sku_code)) {
            throw new ForbiddenHttpException('无效的SKU编码');
        }
        $quantity = Yii::$app->request->post('quantity');
        if ($quantity <= 0) {
            throw new ForbiddenHttpException('无效的数量');
        }
        $model = Cart::findOne(['buyer_id' => Yii::$app->user->id, 'sku_code' => $sku_code]);
        if ($model === null) {
            $model = new Cart();
            $model->quantity = $quantity;
        } else {
            $model->quantity += $quantity;
        }
        $model->sku_code = $sku_code;
        $model->buyer_id = Yii::$app->user->id;
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->getErrors()]);
        }
        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->buyer_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own cart!');
        }
        $quantity = Yii::$app->request->post('quantity');
        if ($quantity <= 0) {
            throw new ForbiddenHttpException('无效的数量');
        }
        $model->quantity = $quantity;
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $model;
    }

    public function actionDelete($id)
    {
        $ids = explode(',', $id);
        if (empty($ids)) {
            throw new ForbiddenHttpException('无效的参数');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($ids as $cart_id) {
                $model = $this->findModel($cart_id);
                if ($model->buyer_id != Yii::$app->user->id) {
                    throw new ForbiddenHttpException('just can update you own cart!');
                }
                $model->delete();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return count($ids);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = Cart::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    public function optional()
    {
        return [
//            'index',
//            'view',
        ];
    }
}