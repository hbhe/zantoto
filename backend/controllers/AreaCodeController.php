<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\AreaCode;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AreaCodeController implements the CRUD actions for AreaCode model.
 */
class AreaCodeController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AreaCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AreaCode::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AreaCode model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AreaCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AreaCode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AreaCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AreaCode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AreaCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AreaCode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AreaCode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSubcat()
    {
        $out = [];
        if (isset($_POST['depdrop_all_params'])) {
            $parent_id = $_POST['depdrop_all_params']['parent_id'];
            $selected_id = $_POST['depdrop_all_params']['selected_id'];
//            if (empty($selected_id)) {
//                $selected_id = $_POST['depdrop_all_params']['selected_district_id'];
//            }
            $out = Yii::$app->db->cache(function ($db) use ($parent_id) {
                return AreaCode::find()->select(['id', 'name'])->where(['parent_id' => $parent_id])->asArray()->all();
            }, YII_DEBUG ? 3 : 24 * 3600);
            return \yii\helpers\Json::encode(['output' => $out, 'selected' => $selected_id]);
        }

        return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionDistrictSubcat()
    {
        $out = [];
        if (isset($_POST['depdrop_all_params'])) {
            $parent_id = $_POST['depdrop_all_params']['area_id'];
            $selected_id = $_POST['depdrop_all_params']['selected_district_id'];
            $out = Yii::$app->db->cache(function ($db) use ($parent_id) {
                return AreaCode::find()->select(['id', 'name'])->where(['parent_id' => $parent_id])->asArray()->all();
            }, YII_DEBUG ? 3 : 24 * 3600);
            return \yii\helpers\Json::encode(['output' => $out, 'selected' => $selected_id]);
        }

        return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
    }

}
