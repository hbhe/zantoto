<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Shop;
use common\models\ShopSearch;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class ShopController extends Controller
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

    public function actions()
    {
        return [
            'logo-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'logo-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    // $img = ImageManagerStatic::make($file->read())->fit(120, 120);
                    // $file->put($img->encode());
                }
            ],
            // http://127.0.0.1/zantoto/backend/web/member/logo-delete?path=\\1\\TZOsI69pdrljbvP3hDG3Tt510JfcduyH.png
            'logo-delete' => [
                'class' => DeleteAction::className() // 删除url对应的动作
            ],

        ];
    }


    /**
     * Lists all Shop models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new ShopSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = Shop::findAll($ids);
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

    public function actionWait()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new ShopSearch();
        $searchModel->seller_status = Shop::SELLER_STATUS_WAIT;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = Shop::findAll($ids);
                foreach ($models as $model) {
                    $model->status = !$model->status;
                    $model->save(false);
                }
            }
        }

        return $this->render('wait', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shop model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->post('accept')) {
            $model->seller_status = Shop::SELLER_STATUS_OK;
            $model->seller_time = date("Y-m-d H:i:s");
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                Yii::$app->session->setFlash('error', '操作失败!');
            } else {
                Yii::$app->session->setFlash('success', '操作成功!');
            }
        }

        if (Yii::$app->request->post('refuse')) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->seller_status = Shop::SELLER_STATUS_REFUSED;
                if (!$model->save(false)) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                    Yii::$app->session->setFlash('error', '操作失败!');
                } else {
                    Yii::$app->session->setFlash('success', '操作成功!');
                }
            }
        }

        if (Yii::$app->request->post('start')) {
            $model->status = Shop::STATUS_OK;
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                Yii::$app->session->setFlash('error', '操作失败!');
            } else {
                Yii::$app->session->setFlash('success', '操作成功!');
            }
        }

        if (Yii::$app->request->post('stop')) {
            $model->status = Shop::STATUS_STOP;
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                Yii::$app->session->setFlash('error', '操作失败!');
            } else {
                Yii::$app->session->setFlash('success', '操作成功!');
            }
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = new Shop();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
            // return $this->redirect(['index']);
        }
        if ($model->hasErrors()) {
            Yii::info([__METHOD__, __LINE__, $model->errors]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Shop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
        if ($model->hasErrors()) {
            Yii::info([__METHOD__, __LINE__, $model->errors]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Shop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shop::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
