<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use common\models\Member;
use common\models\MemberProfile;
use common\models\MemberSearch;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MemberController implements the CRUD actions for Member model.
 */
class MemberController extends Controller
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
            'avatar-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = ImageManagerStatic::make($file->read())->fit(120, 120);
                    $file->put($img->encode());
                }
            ],
            // http://127.0.0.1/zantoto/backend/web/member/avatar-delete?path=\\1\\TZOsI69pdrljbvP3hDG3Tt510JfcduyH.png
            'avatar-delete' => [
                'class' => DeleteAction::className() // 删除url对应的动作
            ],

        ];
    }

    /**
     * Lists all Member models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post('selection');
            if (Yii::$app->request->post('begin')) {
                $models = Member::findAll($ids);
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

    public function actionBankcard()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('bankcard', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Member model.
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
     * Creates a new Member model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = new Member();
        $model->id = Member::generateId();
        $profile = new MemberProfile();

        if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {
            if ($model->validate() && $profile->validate()) {
                if ($model->save(false)) {
                    $profile = $model->memberProfile;
                    $profile->load(Yii::$app->request->post());
                    $profile->save();
                    return $this->goBack();
                    // return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'profile' => $profile,
        ]);
    }

    /**
     * Updates an existing Member model.
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
        $profile = $model->memberProfile;
        if ($profile === null) {
            $profile = new MemberProfile();
            $model->link('memberProfile', $profile);
        }

        if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {
            if ($model->validate() && $profile->validate()) {
                $model->save(false);
                $profile->save(false);
                return $this->goBack();
                // return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'profile' => $profile,
        ]);
    }

    /**
     * Deletes an existing Member model.
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
     * Finds the Member model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Member the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Member::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
