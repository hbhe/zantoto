<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Member;
use common\models\Product;
use common\models\ProductOption;
use common\models\ProductOptionValue;
use common\models\ProductSearch;
use common\models\Sku;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ItemController
 * @package rest\modules\v1\controllers
 *
 */
class ItemController extends ActiveController
{
    public $modelClass = 'common\models\Product';

    public $searchModelClass = 'common\models\ProductSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    /**
     * 我的-店铺卖家-商品列表
     * @param $action
     * @return ActiveDataProvider
     */
    public function prepareDataProvider($action)
    {
        if (!Yii::$app->user->identity->is_seller) {
            throw new NotFoundHttpException('非卖家!');
        }

        $searchModel = new ProductSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['member_id' => Yii::$app->user->id]);
        $dataProvider->query->with('mainImage');
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionCreate()
    {
        $product = new Product();
        $product->load(Yii::$app->request->post(), '');
        $product->member_id = Yii::$app->user->id;
        $product_options = Yii::$app->request->post('product_options');
        if ($product->has_option && empty($product_options)) {
            throw new HttpException(500, "有规格商品需要输入规格!");
        }
        $count = Product::find()->andWhere(['member_id' => Yii::$app->user->id])->count();
        if ($product->member_id != Member::ROOT_ID && $count > 20) {
            throw new HttpException(500, "最多只能创建20个商品!");
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$product->save()) {
                Yii::error([__METHOD__, __LINE__, $product->errors]);
                throw new HttpException(500, "保存失败!");
            }

            foreach ($product_options as $product_option) {
                $productOption = new ProductOption();
                $productOption->attributes = [
                    'product_id' => $product->id,
                    'member_id' => $product->member_id,
                    'name' => $product_option['option_name'],
                ];
                if (!$productOption->save()) {
                    Yii::error([__METHOD__, __LINE__, $productOption->errors]);
                    throw new HttpException(500, "保存属性名失败!");
                }

                foreach ($product_option['option_values'] as $value) {
                    $productOptionValue = new ProductOptionValue();
                    $productOptionValue->attributes = [
                        'product_option_id' => $productOption->id,
                        'name' => $value,
                        //'image' => ''
                    ];
                    if (!$productOptionValue->save()) {
                        Yii::error([__METHOD__, __LINE__, $productOptionValue->errors]);
                        throw new HttpException(500, "保存属性值失败!");
                    }
                }
            }

            // 生成SKU
            $product->initSku();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $product;
    }

    public function actionUpdate($id)
    {
        $product = $this->findModel($id);
        if ($product->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own product!');
        }
        $product->load(Yii::$app->request->post(), '');
        $product_options = Yii::$app->request->post('product_options');
        if ($product->has_option && empty($product_options)) {
            throw new HttpException(500, "有规格商品需要输入规格!");
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$product->save()) {
                Yii::error([__METHOD__, __LINE__, $product->errors]);
                throw new HttpException(500, "保存失败!");
            }

            // 如果未输入options就不动商品SKU
            if (!empty($product_options))
            {
                $product->clearOptionAndSku();

                foreach ($product_options as $product_option) {
                    $productOption = new ProductOption();
                    $productOption->attributes = [
                        'product_id' => $product->id,
                        'member_id' => $product->member_id,
                        'name' => $product_option['option_name'],
                    ];
                    if (!$productOption->save()) {
                        Yii::error([__METHOD__, __LINE__, $productOption->errors]);
                        throw new HttpException(500, "保存属性名失败!");
                    }

                    foreach ($product_option['option_values'] as $value) {
                        $productOptionValue = new ProductOptionValue();
                        $productOptionValue->attributes = [
                            'product_option_id' => $productOption->id,
                            'name' => $value,
                            //'image' => ''
                        ];
                        if (!$productOptionValue->save()) {
                            Yii::error([__METHOD__, __LINE__, $productOptionValue->errors]);
                            throw new HttpException(500, "保存属性值失败!");
                        }
                    }
                }
                $product->initSku();
            }

            Sku::updateAll(['price' => $product->price], ['product_id' => $product->id]);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $product;
    }

    /**
     * 批量修改上下架状态
     * @return array|Product|null
     * @throws ForbiddenHttpException
     */
    public function actionUpdateStatusListing()
    {
        $product_ids = Yii::$app->request->post('product_ids');
        $status_listing = Yii::$app->request->post('status_listing');
        if (empty($product_ids)) {
            throw new ForbiddenHttpException('商品ID不能为空');
        }
        $models = Product::findAll(['id' => $product_ids]);
        foreach ($models as $model) {
            if ($model->member_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException('只能操作自己的商品');
            }
        }
        $n = Product::updateAll(['status_listing' => $status_listing], ['id' => $product_ids]);
        return $n;
    }

    /**
     * 批量修改上下架状态
     * @return array|Product|null
     * @throws ForbiddenHttpException
     */
    public function actionUpdateQuantity()
    {
        $product_ids = Yii::$app->request->post('product_ids');
        $quantity = Yii::$app->request->post('quantity');
        if (empty($product_ids)) {
            throw new ForbiddenHttpException('商品ID不能为空');
        }
        $models = Product::findAll(['id' => $product_ids]);
        foreach ($models as $model) {
            if ($model->member_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException('只能操作自己的商品');
            }
        }
        $n = Sku::updateAll(['quantity' => $quantity], ['product_id' => $product_ids]);
        return $n;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can delete you own product!');
        }
        if ($model->is_platform) {
            throw new ForbiddenHttpException('can not delete platform product!');
        }
        $model->delete();
        return $model;
    }

    public function actionBatchDelete()
    {
        $product_ids = Yii::$app->request->post('product_ids');
        if (empty($product_ids)) {
            throw new ForbiddenHttpException('商品ID不能为空');
        }
        $models = Product::findAll(['id' => $product_ids]);
        $i = 0;
        foreach ($models as $model) {
            if ($model->member_id != Yii::$app->user->id) {
                throw new ForbiddenHttpException('只能操作自己的商品');
            }
            if ($model->is_platform) {
                throw new ForbiddenHttpException('can not delete platform product!');
            }
            if ($model->delete()) {
                $i++;
            }
        }
        return $i;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = Product::find()->where(['id' => $id])->one()) !== null) {
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