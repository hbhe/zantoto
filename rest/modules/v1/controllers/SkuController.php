<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Sku;
use common\models\SkuSearch;
use common\models\Product;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class SkuController
 * @package rest\modules\v1\controllers
 *
 */
class SkuController extends ActiveController
{
    public $modelClass = 'common\models\Sku';

    public $searchModelClass = 'common\models\SkuSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    /**
     * 列表
     * @param $action
     * @return ActiveDataProvider
     */
    public function prepareDataProvider($action)
    {
        if (empty(Yii::$app->request->get('product_id'))) {
            throw new ForbiddenHttpException('Invalid product_id!');
        }
        $searchModel = new SkuSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionCreate()
    {
        $model = new Sku();
        $model->load(Yii::$app->request->post(), '');
        $model->member_id = Yii::$app->user->id;
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->getErrors()]);
        }
        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own cart!');
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
        if ($model->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own cart!');
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
        if (($model = Sku::find()->where(['id' => $id])->one()) !== null) {
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