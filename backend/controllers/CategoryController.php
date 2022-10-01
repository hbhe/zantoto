<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Category;
use common\models\CategorySearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $parent_id = Yii::$app->request->get('parent_id', Category::ROOT_ID);
        $model = Category::findOne($parent_id);
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }
        $parent = Category::findOne(Yii::$app->request->get('parent_id'));
        $model = new Category();
        $model->load(Yii::$app->request->get(), '');
        if ($model->load(Yii::$app->request->post()) && $model->appendTo($parent) && $model->save()) {
            return $this->goBack();
            // return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
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
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->isLeaf()) {
            // \Yii::$app->getSession()->setFlash('success', '保存成功!');
            \Yii::$app->getSession()->setFlash('error', '请先删除其下的子分类!');
            return $this->goBack();
        }
        $model->delete();

        return $this->goBack();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSubcat1()
    {
        // Yii::info($_POST);
        $out = [];
        if (isset($_POST['depdrop_all_params'])) {
            $parent_id = $_POST['depdrop_all_params']['category_id1'];
            $selected_id = $_POST['depdrop_all_params']['selected_category_id2'];
//            if (empty($selected_id)) {
//                $selected_id = $_POST['depdrop_all_params']['selected_district_id'];
//            }
            if (empty($parent_id)) {
                return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
            }
            $out = Yii::$app->db->cache(function ($db) use ($parent_id) {
                return Category::find()->select(['id', 'name'])->where(['parent_id' => $parent_id])->asArray()->all();
            }, YII_DEBUG ? 3 : 60);
            return \yii\helpers\Json::encode(['output' => $out, 'selected' => $selected_id]);
        }

        return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionSubcat2()
    {
        // Yii::info($_POST);
        $out = [];
        if (isset($_POST['depdrop_all_params'])) {
            $parent_id = $_POST['depdrop_all_params']['category_id2'];
            $selected_id = $_POST['depdrop_all_params']['selected_category_id3'];
            if (empty($parent_id)) {
                return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
            }
            $out = Yii::$app->db->cache(function ($db) use ($parent_id) {
                return Category::find()->select(['id', 'name'])->where(['parent_id' => $parent_id])->asArray()->all();
            }, YII_DEBUG ? 3 : 60);
            return \yii\helpers\Json::encode(['output' => $out, 'selected' => $selected_id]);
        }

        return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
    }

}
