<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Wishlist;
use common\models\WishlistSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class WishlistController
 * @package rest\modules\v1\controllers
 *
 */
class WishlistController extends ActiveController
{
    public $modelClass = 'common\models\Wishlist';

    public $searchModelClass = 'common\models\WishlistSearch';

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
        $searchModel = new WishlistSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $member_id = Yii::$app->request->get('member_id', Yii::$app->user->id);
        $dataProvider->query->alias('member')->andWhere(['member.member_id' => $member_id]);
        $title = Yii::$app->request->get('title');
        if (!empty($title)) {
            $dataProvider->query->joinWith('product p');
            $dataProvider->query->andWhere(['like', 'p.title', $title]);
        }
        Yii::info($dataProvider->query->createCommand()->getRawSql());
        return $dataProvider;
    }

    public function actionCreate()
    {
        $model = new Wishlist();
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
            throw new ForbiddenHttpException('just can update you own wishlist!');
        }
        $model->load(Yii::$app->request->post(), '');
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
                if ($model->member_id != Yii::$app->user->id) {
                    throw new ForbiddenHttpException('just can update you own wishlist!');
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
        if (($model = Wishlist::find()->where(['id' => $id])->one()) !== null) {
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