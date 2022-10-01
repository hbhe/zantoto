<?php

namespace storage\controllers;

use trntv\filekit\actions\ViewAction;
use Yii;
use common\models\Picture;
use common\models\PictureSearch;
use common\wosotech\base\Controller;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PictureController implements the CRUD actions for Picture model.
 */
class PictureController extends Controller
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
            'see' => [
                'class' => ViewAction::className(),
                'inline' => false, // true显示, false表示下载
            ]

        ];
    }

    /**
     * Lists all Picture models.
     * @return mixed
     */
    public function actionIndex()
    {
        die('picnic');

        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new PictureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Picture model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        die('picnic');

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Picture model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        die('picnic');

        $model = new Picture();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
	        // return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);        
    }

    /**
     * Updates an existing Picture model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        die('picnic');

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
     * Deletes an existing Picture model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        die('picnic');

        $this->findModel($id)->delete();

        return $this->goBack();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the Picture model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Picture the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Picture::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // 下载, 见trntv\filekit\actions\ViewAction
    public function actionDownload($id, $path)
    {
        $model = $this->findModel($id);
        if ($path != $model->path) {
            die('no permission');
            // throw new HttpException(404, 'No permission');
        }

        $filesystem = Yii::$app->get('fileStorage')->getFilesystem();
        if ($filesystem->has($path) === false) {
            // 再检查一下是否是demo目录下的文件
            $adapter = new \League\Flysystem\Adapter\Local(Yii::getAlias('@backend/web/image-samples/product'));
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            if ($filesystem->has($path) === false) {
                die('the file does not exists!');
            }
        }
        return \Yii::$app->response->sendStreamAsFile(
            $filesystem->readStream($path),
            // pathinfo($path, PATHINFO_BASENAME),
            $model->name,
            [
                'mimeType' => $filesystem->getMimetype($path),
                'inline' => false
            ]
        );
    }

}
