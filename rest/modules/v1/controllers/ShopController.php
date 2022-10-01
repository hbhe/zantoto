<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\CouponLog;
use common\models\Member;
use common\models\Shop;
use common\models\ShopSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ShopController
 * @package rest\modules\v1\controllers
 *
 */
class ShopController extends ActiveController
{
    public $modelClass = 'common\models\Shop';

    public $searchModelClass = 'common\models\ShopSearch';

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
        $searchModel = new ShopSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => Shop::STATUS_OK]);
        $dataProvider->query->andWhere(['seller_status' => Shop::SELLER_STATUS_OK]);
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    /**
     * @return Shop
     */
    public function actionCreate()
    {
        $model = Yii::$app->user->identity->shop;
        if ($model === null) {
            $model = new Shop();
        }
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
            throw new ForbiddenHttpException('just can update you own outlet!');
        }
        $params = Yii::$app->request->post();
        foreach ($params as $key => $value) {
            // 这些字段不允许修改, 只能重新认证
            if (in_array($key, ['company', 'credit_code', 'legal_person', 'legal_identity', 'business_licence_image', 'identity_face_image', 'identity_back_image'])) {
                unset($params[$key]);
            }
        }
        $model->load($params, '');
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update you own outlet!');
        }
        $model->delete();
        return $model;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function actionMine()
    {
        $model = Shop::find()->where(['member_id' => Yii::$app->user->id])->limit(1)->one();
        if ($model === null) {
            throw new ForbiddenHttpException('请先提交认证!');
        }
        return $model;
    }

    public function findModel($id)
    {
        if (($model = Shop::find()->where(['id' => $id])->one()) !== null) {
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