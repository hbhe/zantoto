<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Model;
use common\models\ShopCategory;
use common\models\ShopCategorySearch;
use Yii;
use yii\base\Exception;
use yii\bootstrap\ActiveForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ShopCategoryController implements the CRUD actions for ShopCategory model.
 */
class ShopCategoryController extends Controller
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
            'tabular-input' => [
                'class' => \unclead\multipleinput\examples\actions\TabularInputAction::class,
            ],
            'multiple-input' => [
                'class' => \unclead\multipleinput\examples\actions\MultipleInputAction::class,
            ],
            'embedded-input' => [
                'class' => \unclead\multipleinput\examples\actions\EmbeddedInputAction::class,
            ],
        ];
    }

    /**
     * Lists all ShopCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new ShopCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = ShopCategory::findAll($ids);
                foreach ($models as $model) {
                    $model->status = !$model->status;
                    $model->save(false);
                }
            }
        }

        $model = new ShopCategory();
        $model->load(Yii::$app->request->get(), '');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->goBack();
            return $this->refresh();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single ShopCategory model.
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
     * Creates a new ShopCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = new ShopCategory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
            // return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShopCategory model.
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
     * Deletes an existing ShopCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->getChildren()->count()) {
            \Yii::$app->getSession()->setFlash('error', '请先删除子分类后再删除父分类!');
            return $this->goBack();
        }
        $model->delete();

        return $this->goBack();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the ShopCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShopCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShopCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSubcat()
    {
        $out = [];
        if (isset($_POST['depdrop_all_params'])) {
            $parent_id = $_POST['depdrop_all_params']['parent_shop_category_id'];
            $selected_id = $_POST['depdrop_all_params']['selected_shop_category_id'];
            $out = Yii::$app->db->cache(function ($db) use ($parent_id) {
                return ShopCategory::find()->select(['id', 'name'])->where(['parent_id' => $parent_id])->asArray()->all();
            }, YII_DEBUG ? 3 : 24 * 3600);
            return \yii\helpers\Json::encode(['output' => $out, 'selected' => $selected_id]);
        }

        return \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionChildren()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        if (!Yii::$app->request->get('parent_id')) {
            return $this->goBack();
        }

        $searchModel = new ShopCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->indexBy('id');
        $dataProvider->query->orderBy(['sort_order' => SORT_ASC]);
        $dataProvider->pagination = false;
        $models = $dataProvider->getModels();
        $request = Yii::$app->getRequest();
        if ($request->isPost) {
            $oldIDs = ArrayHelper::getColumn($models, 'id');

            $newModels = [];
            $post = Yii::$app->request->post('ShopCategory');
            if (!empty($post)) {
                foreach ($post as $i => $item) {
                    if (isset($models[$i])) {
                        $newModels[$i] = $models[$i];
                    } else {
                        $newModels[$i] = new ShopCategory;
                    }
                }
            }

            Model::loadMultiple($newModels, Yii::$app->request->post());
            $i = 0;
            foreach ($newModels as $model) { // load sort_order
                $model->sort_order = $i++;
                $model->parent_id = Yii::$app->request->get('parent_id');
            }

            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::getColumn($newModels, 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($newModels);
            }

            if (Model::validateMultiple($newModels)) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    ShopCategory::deleteAll(['id' => $deletedIDs]);
                    foreach ($newModels as $model) {
                        if (!$model->save(false)) {
                            Yii::error([__METHOD__, __LINE__, $model->errors]);
                            throw new Exception('save failed.');
                        }
                    }
                    $transaction->commit();
                    //return $this->goBack();

                    \Yii::$app->getSession()->setFlash('success', '保存成功!');
                    return $this->refresh();
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('children', [
            'models' => $models
        ]);

    }

}
