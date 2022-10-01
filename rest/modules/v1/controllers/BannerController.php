<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Banner;
use common\models\BannerSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class BannerController
 * @package rest\modules\v1\controllers
 *
 */
class BannerController extends ActiveController
{
    public $modelClass = 'common\models\Banner';

    public $searchModelClass = 'common\models\BannerSearch';

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
        $searchModel = new BannerSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => Banner::STATUS_ACTIVE]);
        // Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = Banner::find()->where(['id' => $id])->one()) !== null) {
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