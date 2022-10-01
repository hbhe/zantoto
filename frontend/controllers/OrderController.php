<?php

namespace frontend\controllers;

use common\models\MemberOrder;
use common\models\MemberOrderSearch;
use common\models\Order;
use common\models\OrderSearch;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
                    'publish' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'picture-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'picture-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    //$img = ImageManagerStatic::make($file->read())->fit(215, 215);
                    //$file->put($img->encode());
                }
            ],
            'picture-delete' => [
                'class' => DeleteAction::className(),
            ],

        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->andWhere(['status' => array_keys(Order::getStatusOptions2())]);

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = Order::findAll($ids);
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
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->user->setReturnUrl(Url::current());
        $params = Yii::$app->request->queryParams;
        unset($params['id']);
        $searchModel = new MemberOrderSearch();
        $searchModel->order_id = $id;
        $dataProvider = $searchModel->search($params);

        if (Yii::$app->request->post('finish')) {
            foreach ($model->memberOrders as $memberOrder) {
                if (in_array($memberOrder->status, [MemberOrder::STATUS_WAIT, MemberOrder::STATUS_ASSIGNED])) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                    Yii::$app->session->setFlash('danger', '尚有接单未处理!');
                    return $this->refresh();
                }
            }
            $model->status = Order::STATUS_SUBMITED;
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                Yii::$app->session->setFlash('danger', '保存失败!');
            } else {
                Yii::$app->session->setFlash('success', '保存成功!');
            }
            return $this->refresh();
        }

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'member' => $model->member,
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (empty($model->order_tags)) {
                $model->addError('order_tags', '订单类型不能为空');
                goto end;
            }
            if (empty($model->skill_tags)) {
                $model->addError('skill_tags', '技能不能为空');
                goto end;
            }
            if (empty($model->brand_tags)) {
                $model->addError('brand_tags', '品牌不能为空');
                goto end;
            }

            if (isset($_POST['nowait'])) {
                $model->status = Order::STATUS_PUBLISHED;
            }

            if ($model->save(false)) {
                return $this->goBack();
            }
        }
        end:
        if ($model->hasErrors()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
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
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (empty($model->order_tags)) {
                $model->addError('order_tags', '订单类型不能为空');
                goto end;
            }
            if (empty($model->skill_tags)) {
                $model->addError('skill_tags', '技能不能为空');
                goto end;
            }
            if (empty($model->brand_tags)) {
                $model->addError('brand_tags', '品牌不能为空');
                goto end;
            }
            if ($model->save(false)) {
                return $this->goBack();
            }
        }
        end:
        if ($model->hasErrors()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
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
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionWait()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => array_keys(Order::getStatusOptions())]);

        if (Yii::$app->request->isPost) {
            Yii::info([$_GET, $_POST]);
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('publish')) {
                $models = Order::findAll($ids);
                foreach ($models as $model) {
                    $model->status = Order::STATUS_PUBLISHED;
                    $model->save(false);
                }
            }
            if (Yii::$app->request->post('refuse')) {
                $searchModelPanel = new OrderSearch();
                $searchModelPanel->load(Yii::$app->request->post());
                $models = Order::findAll($ids);
                foreach ($models as $model) {
                    $model->status = Order::STATUS_REFUSED;
                    $model->reason = $searchModelPanel->reason;
                    $model->save(false);
                }
            }
        }

        return $this->render('wait', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionPublish($id)
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = $this->findModel($id);
        $model->status = Order::STATUS_PUBLISHED;
        if (!$model->save(false)) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $this->goBack();

    }

}
