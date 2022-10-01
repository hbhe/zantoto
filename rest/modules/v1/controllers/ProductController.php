<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\ActiveRecord;
use common\models\Hotkey;
use common\models\Product;
use common\models\ProductSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ProductController
 * @package rest\modules\v1\controllers
 *
 */
class ProductController extends ActiveController
{
    public $modelClass = 'common\models\Product';

    public $searchModelClass = 'common\models\ProductSearch';

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
        $searchModel = new ProductSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status_listing' => Product::STATUS_LISTING_ON]);
        $dataProvider->query->with('mainImage');
        if (!empty($searchModel->q)) {
            $keys = StringHelper::explode($searchModel->q, ' ', true, true);
        }
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->scenario = ActiveRecord::SCENARIO_VIEW;
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
            'index',
            'view',
        ];
    }
}