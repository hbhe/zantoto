<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Member;
use common\models\Model;
use common\models\Product;
use common\models\ProductOption;
use common\models\ProductOptionSearch;
use common\models\ProductOptionValue;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

//use yii\base\Model;

/**
 * ProductOptionController implements the CRUD actions for ProductOption model.
 */
class ProductOptionController extends Controller
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
                    'init' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProductOption models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $model = Product::findOne(Yii::$app->request->get('product_id'));
        if ($model === null) {
            Yii::$app->session->setFlash('error', '无效的商品ID!');
            return $this->goBack();
        }
        $searchModel = new ProductOptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = ProductOption::findAll($ids);
                foreach ($models as $model) {
                    $model->status = !$model->status;
                    $model->save(false);
                }
            }
        }

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductOption model.
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
     * Finds the ProductOption model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductOption the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductOption::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionInit()
    {
        $product = Product::findOne($product_id = Yii::$app->request->get('product_id'));
        if ($product === null) {
            Yii::$app->session->setFlash('error', '无效的商品ID!');
            return $this->goBack();
            // return $this->redirect(['index', 'product_id' => $product_id]);
        }

        $category = $product->category;
        foreach ($category->options as $option) {
            if (ProductOption::findOne(['product_id' => $product->id, 'option_id' => $option->id])) {
                continue;
            }
            $product->link('options', $option, ['member_id' => Member::ROOT_ID]);
        }
        return $this->goBack();
    }

    public function actionCreate()
    {
        $modelCatalogOption = new ProductOption();
        $modelsOptionValue = [new ProductOptionValue()];

        if ($modelCatalogOption->load(Yii::$app->request->post())) {
            $modelsOptionValue = Model::createMultiple(ProductOptionValue::classname());
            Model::loadMultiple($modelsOptionValue, Yii::$app->request->post());
            foreach ($modelsOptionValue as $index => $modelOptionValue) {
                $modelOptionValue->sort_order = $index;
                //$modelOptionValue->img = \yii\web\UploadedFile::getInstance($modelOptionValue, "[{$index}]img");
            }
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsOptionValue),
                    ActiveForm::validate($modelCatalogOption)
                );
            }
            // validate all models
            $valid = $modelCatalogOption->validate();
            if (!$valid) {
                Yii::error(['err', $modelCatalogOption->errors]);
            }
            $valid = Model::validateMultiple($modelsOptionValue) && $valid;
            if (!$valid) {
                Yii::error(['error', __METHOD__, __LINE__]);
            }

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelCatalogOption->save(false)) {
                        foreach ($modelsOptionValue as $modelOptionValue) {
                            $modelOptionValue->product_option_id = $modelCatalogOption->id;
                            if (($flag = $modelOptionValue->save(false)) === false) {
                                Yii::error(['err', $modelOptionValue->errors]);
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        //return $this->redirect(['view', 'id' => $modelCatalogOption->id]);
                    }

                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'modelCatalogOption' => $modelCatalogOption,
            'modelsOptionValue' => (empty($modelsOptionValue)) ? [new ProductOptionValue] : $modelsOptionValue
        ]);

    }

    public function actionUpdate($id)
    {
        $modelCatalogOption = $this->findModel($id);
        $modelsOptionValue = $modelCatalogOption->productOptionValues;
        if ($modelCatalogOption->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsOptionValue, 'id', 'id');
            $modelsOptionValue = Model::createMultiple(ProductOptionValue::classname(), $modelsOptionValue);
            Model::loadMultiple($modelsOptionValue, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsOptionValue, 'id', 'id')));
            $deletedIDs = array_values($deletedIDs);
            foreach ($modelsOptionValue as $index => $modelOptionValue) {
                $modelOptionValue->sort_order = $index;
                //$modelOptionValue->img = \yii\web\UploadedFile::getInstance($modelOptionValue, "[{$index}]img");
            }

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsOptionValue),
                    ActiveForm::validate($modelCatalogOption)
                );
            }

            // validate all models
            $valid = $modelCatalogOption->validate();
            $valid = Model::validateMultiple($modelsOptionValue) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelCatalogOption->save(false)) {
                        if (!empty($deletedIDs)) {
                            $flag = ProductOptionValue::deleteAll(['id' => $deletedIDs]);
                        }

                        if ($flag) {
                            foreach ($modelsOptionValue as $modelOptionValue) {
                                $modelOptionValue->product_option_id = $modelCatalogOption->id;
                                if (($flag = $modelOptionValue->save(false)) === false) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $modelCatalogOption->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }


        return $this->render('update', [
            'modelCatalogOption' => $modelCatalogOption,
            'modelsOptionValue' => (empty($modelsOptionValue)) ? [new ProductOptionValue] : $modelsOptionValue
        ]);

    }


    /**
     * Deletes an existing CatalogOption model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $optonValuesIDs = ArrayHelper::map($model->productOptionValues, 'id', 'id');
        $optonValuesIDs = array_values($optonValuesIDs);
        ProductOptionValue::deleteAll(['id' => $optonValuesIDs]);
        $name = $model->name;
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Record  <strong>"' . $name . '"</strong> deleted successfully.');
        }
        return $this->redirect(['index']);
    }
}
