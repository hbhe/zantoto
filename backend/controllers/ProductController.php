<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Model;
use common\models\Product;
use common\models\ProductOption;
use common\models\ProductOptionSearch;
use common\models\ProductOptionValue;
use common\models\ProductSearch;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
                    'init-category-option' => ['POST'],
                    'init-sku' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'product-pictures-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'product-pictures-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    //$img = ImageManagerStatic::make($file->read())->fit(215, 215);
                    //$file->put($img->encode());
                }
            ],
            'product-pictures-delete' => [
                'class' => DeleteAction::className(),
            ],

            'detail-pictures-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'detail-pictures-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    //$img = ImageManagerStatic::make($file->read())->fit(215, 215);
                    //$file->put($img->encode());
                }
            ],
            'detail-pictures-delete' => [
                'class' => DeleteAction::className(),
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new ProductSearch();
        // $searchModel->is_platform = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('mainImage');

        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = Product::findAll($ids);
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
     * Displays a single Product model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new ProductOptionSearch();
        $searchModel->product_id = $id;
        $dataProvider = $searchModel->search([]);
        $dataProvider->query->orderBy(['sort_order' => SORT_ASC]);
        $dataProvider->pagination = false;
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $modelProduct = new Product;
        // $modelProduct->member_id = Member::ROOT_ID;
        $modelsProductOption = [new ProductOption];
        $modelsProductOptionValue = [[new ProductOptionValue]];

        if ($modelProduct->load(Yii::$app->request->post())) {
            // Yii::info(Yii::$app->request->post());
            $modelsProductOption = Model::createMultiple(ProductOption::classname());
            Model::loadMultiple($modelsProductOption, Yii::$app->request->post());
            foreach ($modelsProductOption as $index => &$ar) {
                // Yii::info(['aaa', $index, $ar->name]);
                $ar->sort_order = $index;
                //$son->img = \yii\web\UploadedFile::getInstance($son, "[{$index}]img");
            }
            unset($ar);

            // validate person and houses models
            $valid = $modelProduct->validate();
            if (!$valid) {
                Yii::info([__METHOD__, __LINE__, $modelProduct->errors]);
            }
            $valid = Model::validateMultiple($modelsProductOption) && $valid;
            if (!$valid) {
                foreach ($modelsProductOption as $ar) {
                    if ($ar->hasErros()) {
                        Yii::error([__METHOD__, __LINE__, $ar->errors]);
                    }
                }
            }

            if (isset($_POST['ProductOptionValue'][0][0])) {
                foreach ($_POST['ProductOptionValue'] as $indexProductOption => $productOptionValues) {
                    foreach ($productOptionValues as $indexProductOptionValue => $productOptionValue) {
                        $data['ProductOptionValue'] = $productOptionValue;
                        $modelProductOptionValue = new ProductOptionValue;
                        $modelProductOptionValue->load($data);
                        $modelsProductOptionValue[$indexProductOption][$indexProductOptionValue] = $modelProductOptionValue;
                        $valid = $modelProductOptionValue->validate();
                        if (!$valid) {
                            Yii::error([__METHOD__, __LINE__, $modelProductOptionValue->errors]);
                        }
                    }
                }
            }

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelProduct->save(false)) {
                        foreach ($modelsProductOption as $indexProductOption => $modelProductOption) {
                            if ($flag === false) {
                                break;
                            }

                            $modelProductOption->product_id = $modelProduct->id;

                            if (!($flag = $modelProductOption->save(false))) {
                                Yii::error([__METHOD__, __LINE__, $modelProductOption->errors]);
                                break;
                            }

                            if (isset($modelsProductOptionValue[$indexProductOption]) && is_array($modelsProductOptionValue[$indexProductOption])) {
                                foreach ($modelsProductOptionValue[$indexProductOption] as $indexProductOptionValue => $modelProductOptionValue) {
                                    $modelProductOptionValue->product_option_id = $modelProductOption->id;
                                    if (!($flag = $modelProductOptionValue->save(false))) {
                                        Yii::error([__METHOD__, __LINE__, $modelProductOptionValue->errors]);
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        // 如果修改了has_option, 就要初始化OPTION&SKU
                        if ($modelProduct->has_option_changed_flag) {
                            $modelProduct->initCategoryOption();
                        }

                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $modelProduct->id]);
                        return $this->goBack();
                    } else {
                        $transaction->rollBack();
                        Yii::error([__METHOD__, __LINE__, 'Create product failed']);
                        if (YII_ENV_DEV) throw new HttpException(500, 'Create product failed');
                    }
                } catch (Exception $e) {
                    Yii::error([__METHOD__, __LINE__, $e->getMessage()]);
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }

        return $this->render('create', [
            'model' => $modelProduct,
            'modelsProductOption' => (empty($modelsProductOption)) ? [new ProductOption] : $modelsProductOption,
            'modelsProductOptionValue' => (empty($modelsProductOptionValue)) ? [[new ProductOptionValue]] : $modelsProductOptionValue,
        ]);
    }


    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     *
     * 对规格的增删或者规模取值的增删都会先清除已存在的SKU数据, 请谨慎操作!
     */
    public function actionUpdate($id)
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $modelProduct = $this->findModel($id);
        $modelsProductOption = $modelProduct->productOptions;
        $modelsProductOptionValue = [];
        $oldProductOptionValues = [];

        if (!empty($modelsProductOption)) {
            foreach ($modelsProductOption as $indexProductOption => $modelProductOption) {
                $productOptionValues = $modelProductOption->productOptionValues;
                $modelsProductOptionValue[$indexProductOption] = $productOptionValues;
                $oldProductOptionValues = ArrayHelper::merge(ArrayHelper::index($productOptionValues, 'id'), $oldProductOptionValues);
            }
        }

        if ($modelProduct->load(Yii::$app->request->post())) {
            $modelsProductOptionValue = [];
            $oldProductOptionIDs = ArrayHelper::map($modelsProductOption, 'id', 'id');
            $modelsProductOption = Model::createMultiple(ProductOption::classname(), $modelsProductOption);
            Model::loadMultiple($modelsProductOption, Yii::$app->request->post());
            foreach ($modelsProductOption as $index => &$ar) {
                $ar->sort_order = $index;
                // $son->img = \yii\web\UploadedFile::getInstance($son, "[{$index}]img");
            }
            unset($ar);

            $deletedProductOptionIDs = array_diff($oldProductOptionIDs, array_filter(ArrayHelper::map($modelsProductOption, 'id', 'id')));

            // validate person and houses models
            $valid = $modelProduct->validate();
            $valid = Model::validateMultiple($modelsProductOption) && $valid;

            $productOptionValuesIDs = [];
            if (isset($_POST['ProductOptionValue'][0][0])) {
                foreach ($_POST['ProductOptionValue'] as $indexProductOption => $productOptionValues) {
                    $productOptionValuesIDs = ArrayHelper::merge($productOptionValuesIDs, array_filter(ArrayHelper::getColumn($productOptionValues, 'id')));
                    foreach ($productOptionValues as $indexProductOptionValue => $productOptionValue) {
                        $data['ProductOptionValue'] = $productOptionValue;
                        $modelProductOptionValue = (isset($productOptionValue['id']) && isset($oldProductOptionValues[$productOptionValue['id']])) ? $oldProductOptionValues[$productOptionValue['id']] : new ProductOptionValue;
                        $modelProductOptionValue->load($data);
                        $modelsProductOptionValue[$indexProductOption][$indexProductOptionValue] = $modelProductOptionValue;
                        $valid = $modelProductOptionValue->validate();
                    }
                }
            }

            $oldProductOptionValuesIDs = ArrayHelper::getColumn($oldProductOptionValues, 'id');
            $deletedProductOptionValuesIDs = array_diff($oldProductOptionValuesIDs, $productOptionValuesIDs);
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelProduct->save(false)) {
                        if (!empty($deletedProductOptionValuesIDs)) {
                            ProductOptionValue::deleteAll(['id' => $deletedProductOptionValuesIDs]);
                        }

                        if (!empty($deletedProductOptionIDs)) {
                            ProductOption::deleteAll(['id' => $deletedProductOptionIDs]);
                        }

                        foreach ($modelsProductOption as $indexProductOption => $modelProductOption) {
                            if ($flag === false) {
                                break;
                            }

                            $modelProductOption->product_id = $modelProduct->id;
                            if (!($flag = $modelProductOption->save(false))) {
                                Yii::error([__METHOD__, __LINE__, $modelProductOption->errors]);
                                break;
                            }

                            if (isset($modelsProductOptionValue[$indexProductOption]) && is_array($modelsProductOptionValue[$indexProductOption])) {
                                foreach ($modelsProductOptionValue[$indexProductOption] as $indexProductOptionValue => $modelProductOptionValue) {
                                    $modelProductOptionValue->product_option_id = $modelProductOption->id;
                                    if (!($flag = $modelProductOptionValue->save(false))) {
                                        Yii::error([__METHOD__, __LINE__, $modelProductOptionValue->errors]);
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        Yii::error([__METHOD__, __LINE__, $modelProduct->errors]);
                    }

                    if ($flag) {
                        // 如果需要规格且当前规格数为0, 则将对应category下的规格导入
                        // $modelProduct->checkOptionStatus();

                        // 如果修改了has_option, 就要初始化OPTION&SKU
                        if ($modelProduct->has_option_changed_flag) {
                            $modelProduct->initCategoryOption();
                        }

                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $modelProduct->id]);
                        // return $this->goBack();
                    } else {
                        $transaction->rollBack();
                        Yii::error([__METHOD__, __LINE__, 'Update product failed']);
                        if (YII_ENV_DEV) throw new HttpException(500, 'Update product failed');
                    }
                } catch (Exception $e) {
                    Yii::error([__METHOD__, __LINE__, $e->getMessage()]);
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }

        return $this->render('update', [
            'model' => $modelProduct,
            'modelsProductOption' => (empty($modelsProductOption)) ? [new ProductOption] : $modelsProductOption,
            'modelsProductOptionValue' => (empty($modelsProductOptionValue)) ? [[new ProductOptionValue]] : $modelsProductOptionValue
        ]);
    }

    /**
     * Deletes an existing Product model.
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
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionInitSku($id)
    {
        $product = Product::findOne(['id' => $id]);
        $product->initSku();
        \Yii::$app->getSession()->setFlash('success', '重新生成SKU成功!');
        return $this->goBack();
    }

    public function actionInitCategoryOption($id)
    {
        $product = Product::findOne(['id' => $id]);
        $product->initCategoryOption();
        \Yii::$app->getSession()->setFlash('success', '重新获取规格成功!');
        return $this->goBack();
    }

}

/*
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        // Yii::error($_POST);
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
            // return $this->redirect(['view', 'id' => $model->id]);
        }
        if ($model->hasErrors()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
        return $this->render('_create', [
            'model' => $model,
        ]);
    }

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
*/
