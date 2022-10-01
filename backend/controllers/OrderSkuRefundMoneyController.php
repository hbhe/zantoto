<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Member;
use common\models\OrderSkuRefund;
use common\models\OrderSkuRefundSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrderSkuRefundController implements the CRUD actions for OrderSkuRefund model.
 */
class OrderSkuRefundMoneyController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderSkuRefund models.
     * @return mixed
     * 退货
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new OrderSkuRefundSearch();
        $searchModel->member_id = Member::ROOT_ID;
        $searchModel->need_ship = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = OrderSkuRefund::findAll($ids);
                foreach ($models as $model) {
                    $model->status = !$model->status;
                    $model->save(false);
                }
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderSkuRefund model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = $this->findModel($id);
        if (isset($_POST['accept'])) {
            $model->status = OrderSkuRefund::STATUS_ACCEPT;
            $model->handled_by = Yii::$app->user->id;
            $model->handled_time = date("Y-m-d H:i:s");
        }
        if (isset($_POST['refuse'])) {
            $model->status = OrderSkuRefund::STATUS_REFUSED;
            $model->handled_by = Yii::$app->user->id;
            $model->handled_time = date("Y-m-d H:i:s");
        }
        if (isset($_POST['confirm'])) {
            $model->status = OrderSkuRefund::STATUS_CONFIRM;
            $model->shipping_by = Yii::$app->user->id;
            $model->shipping_time = date("Y-m-d H:i:s");
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '操作成功!');
            return $this->goBack();
            //return $this->refresh();
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new OrderSkuRefund model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = new OrderSkuRefund();
        $model->member_id = Member::ROOT_ID;
        $model->need_ship = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
            // return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderSkuRefund model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
            // return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderSkuRefund model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the OrderSkuRefund model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderSkuRefund the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderSkuRefund::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
