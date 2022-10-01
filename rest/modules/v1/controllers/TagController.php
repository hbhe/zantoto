<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Tag;
use common\models\TagSearch;
use rest\controllers\ActiveController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Class TagController
 * @package rest\modules\v1\controllers
 *
 */
class TagController extends ActiveController
{
    public $modelClass = 'common\models\Tag';

    public $searchModelClass = 'common\models\TagSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $searchModel = new TagSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => Tag::STATUS_ENABLE]);
        return $dataProvider;
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Tag::find(),
        ]);

        return $dataProvider;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = Tag::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    /*
     * 定义不需登录认证就可访问的页面
     */
    public function optional()
    {
        return [
            'index',
            'view',
        ];
    }
}