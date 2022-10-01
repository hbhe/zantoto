<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\RevenueLog;
use common\models\RevenueLogSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class RevenueLogController
 * @package rest\modules\v1\controllers
 *
 */
class RevenueLogController extends ActiveController
{
    public $modelClass = 'common\models\RevenueLog';

    public $searchModelClass = 'common\models\RevenueLog';

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
        $searchModel = new RevenueLogSearch;
        $params = Yii::$app->request->queryParams;
        if (ArrayHelper::getValue($params, 'kind', '') != '') {
            $searchModel->status = explode(',', $params['kind']);
            unset($params['kind']);
        }
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['member_id' => Yii::$app->user->id]);
        $dataProvider->query->andWhere(['status' => RevenueLog::STATUS_CLEARED]);
        return $dataProvider;
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionRevenueDetail()
    {
        $searchModel = new RevenueLogSearch;
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['member_id' => Yii::$app->user->id]);
        $dataProvider->query->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE, RevenueLog::KIND_COMMISSION_CHILD_CASHOUT, RevenueLog::KIND_COMMISSION_NEW_STAR]]);
        return $dataProvider;
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionUnclear()
    {
        $searchModel = new RevenueLogSearch;
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['member_id' => Yii::$app->user->id]);
        $dataProvider->query->uncleared();
        $dataProvider->query->groupBy(['order_id'])->orderBy(['created_at' => SORT_DESC]);
        return $dataProvider;
    }

    public function actionCommissionStat()
    {
        $key = [__METHOD__, Yii::$app->user->id];
        $arr = Yii::$app->cache->get($key);
        if ($arr !== false) {
            return $arr;
        }

        $amount_total = RevenueLog::find()
            ->andWhere(['member_id' => Yii::$app->user->id])
            ->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE, RevenueLog::KIND_COMMISSION_NEW_STAR, RevenueLog::KIND_COMMISSION_CHILD_CASHOUT]])
            ->sum('amount');

        $amount_from_sale = RevenueLog::find()
            ->andWhere(['member_id' => Yii::$app->user->id])
            ->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE]])
            ->sum('amount');

        $amount_from_cashout = RevenueLog::find()
            ->andWhere(['member_id' => Yii::$app->user->id])
            ->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_NEW_STAR, RevenueLog::KIND_COMMISSION_CHILD_CASHOUT]])
            ->sum('amount');

        $amount_today = RevenueLog::find()
            ->andWhere(['member_id' => Yii::$app->user->id])
            ->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE, RevenueLog::KIND_COMMISSION_NEW_STAR, RevenueLog::KIND_COMMISSION_CHILD_CASHOUT]])
            ->today()
            ->sum('amount');

        $amount_week = RevenueLog::find()
            ->andWhere(['member_id' => Yii::$app->user->id])
            ->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE, RevenueLog::KIND_COMMISSION_NEW_STAR, RevenueLog::KIND_COMMISSION_CHILD_CASHOUT]])
            ->week()
            ->sum('amount');

        $amount_month = RevenueLog::find()
            ->andWhere(['member_id' => Yii::$app->user->id])
            ->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE, RevenueLog::KIND_COMMISSION_NEW_STAR, RevenueLog::KIND_COMMISSION_CHILD_CASHOUT]])
            //->theMonth()
            ->month()
            ->sum('amount');

        $date = date("Y-m-d",strtotime("-1 month"));
        $amount_last_month = RevenueLog::find()
            ->andWhere(['member_id' => Yii::$app->user->id])
            ->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE, RevenueLog::KIND_COMMISSION_NEW_STAR, RevenueLog::KIND_COMMISSION_CHILD_CASHOUT]])
            ->andWhere([
                'YEAR(created_at)' => new \yii\db\Expression("YEAR(\"$date\")"),
                'MONTH(created_at)' => new \yii\db\Expression("MONTH(\"$date\")"),
            ])
            ->sum('amount');

        $arr['amount_total'] = empty($amount_total) ? 0 : $amount_total;
        $arr['amount_from_sale'] = empty($amount_from_sale) ? 0 : $amount_from_sale;
        $arr['amount_from_cashout'] = empty($amount_from_cashout) ? 0 : $amount_from_cashout;
        $arr['amount_today'] = empty($amount_today) ? 0 : $amount_today;
        $arr['amount_week'] = empty($amount_week) ? 0 : $amount_week;
        $arr['amount_month'] = empty($amount_month) ? 0 : $amount_month;
        $arr['amount_last_month'] = empty($amount_last_month) ? 0 : $amount_last_month;

        Yii::$app->cache->set($key, $arr, YII_ENV_DEV ? 6 : 60);
        return $arr;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = RevenueLog::find()->where(['id' => $id])->one()) !== null) {
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