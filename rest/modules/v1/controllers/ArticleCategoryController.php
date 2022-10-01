<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use rest\controllers\ActiveController;
use rest\models\ArticleCategory;
use rest\models\ArticleCategorySearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class ArticleCategoryController
 * @package rest\modules\v1\controllers
 *
 * 资讯分类列表
 * 127.0.0.1/zantoto/rest/web/v1/article-categories
 *
 * 资讯分类详情
 * 127.0.0.1/zantoto/rest/web/v1/article-categories/1
 *
 */
class ArticleCategoryController extends ActiveController
{
    public $modelClass = 'common\models\ArticleCategory';

    public $searchModelClass = 'common\models\ArticleCategorySearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $searchModel = new ArticleCategorySearch;
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = ArticleCategory::find()->where(['id' => $id])->one()) !== null) {
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