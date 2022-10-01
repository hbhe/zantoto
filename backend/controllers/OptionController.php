<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Model;
use common\models\Option;
use common\models\OptionSearch;
use common\models\OptionValue;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * OptionController implements the CRUD actions for Option model.
 */
class OptionController extends Controller
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
     * Lists all Option models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new OptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new Option();
        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Option model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the Option model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Option the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Option::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $parent = new Option();
        $sons = [new OptionValue()];

        if ($parent->load(Yii::$app->request->post())) {
            $sons = Model::createMultiple(OptionValue::classname());
            Model::loadMultiple($sons, Yii::$app->request->post());
            foreach ($sons as $index => $son) {
                $son->sort_order = $index;
                //$son->img = \yii\web\UploadedFile::getInstance($son, "[{$index}]img");
            }
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($sons),
                    ActiveForm::validate($parent)
                );
            }
            // validate all models
            $valid = $parent->validate();
            if (!$valid) {
                Yii::error([__METHOD__, __LINE__, $parent->errors]);
            }

            $valid = Model::validateMultiple($sons) && $valid;
            if (!$valid) {
                foreach ($sons as $son) {
                    if ($son->hasErrors()) {
                        Yii::error([__METHOD__, __LINE__, $parent->errors]);
                    }
                }
            }

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $parent->save(false)) {
                        foreach ($sons as $son) {
                            $son->option_id = $parent->id;
                            if (($flag = $son->save(false)) === false) {
                                Yii::error([__METHOD__, __LINE__, $son->errors]);
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->goBack();
                    }

                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'parent' => $parent,
            'sons' => (empty($sons)) ? [new OptionValue] : $sons
        ]);

    }

    public function actionUpdate($id)
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $parent = $this->findModel($id);
        $sons = $parent->values;
        if ($parent->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($sons, 'id', 'id');
            $sons = Model::createMultiple(OptionValue::classname(), $sons);
            Model::loadMultiple($sons, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($sons, 'id', 'id')));
            $deletedIDs = array_values($deletedIDs);
            foreach ($sons as $index => $son) {
                $son->sort_order = $index;
                //$son->img = \yii\web\UploadedFile::getInstance($son, "[{$index}]img");
            }

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($sons),
                    ActiveForm::validate($parent)
                );
            }

            // validate all models
            $valid = $parent->validate();
            $valid = Model::validateMultiple($sons) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $parent->save(false)) {
                        if (!empty($deletedIDs)) {
                            // $flag = OptionValue::deleteByIDs($deletedIDs);
                            $flag = OptionValue::deleteAll(['id' => $deletedIDs]);
                        }

                        if ($flag) {
                            foreach ($sons as $son) {
                                $son->option_id = $parent->id;
                                if (($flag = $son->save(false)) === false) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->goBack();
                        // return $this->redirect(['view', 'id' => $parent->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'parent' => $parent,
            'sons' => (empty($sons)) ? [new OptionValue] : $sons
        ]);

    }

}
