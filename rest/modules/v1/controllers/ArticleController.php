<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use rest\controllers\ActiveController;
use rest\models\Article;
use rest\models\ArticleSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Class ArticleController
 * @package rest\modules\v1\controllers
 *
 * 文章列表
 * http://127.0.0.1/zantoto/rest/web/v1/articles?status=1&article_category_id=1
 * http://127.0.0.1/zantoto/rest/web/v1/articles
 *
 * 文章详情
 * http://127.0.0.1/zantoto/rest/web/v1/articles/1?expand=articleCategory
 *
 */
class ArticleController extends ActiveController
{
    public $modelClass = 'rest\models\Article';

    public $searchModelClass = 'rest\models\ArticleSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $searchModel = new ArticleSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => 1]);
        return $dataProvider;
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Article::find(),
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
        if (($model = Article::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    public function optional()
    {
        return [
            'index',
            'view',
        ];
    }
}