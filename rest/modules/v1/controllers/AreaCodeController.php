<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\AreaCode;
use common\models\AreaCodeSearch;
use common\wosotech\helper\Util;
use rest\controllers\ActiveController;
use rest\models\Member;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class AreaCodeController
 * @package rest\modules\v1\controllers
 *
 * 得到省份列表
 * http://127.0.0.1/zantoto/rest/web/v1/area-codes?parent_id=1
 *
 * 得到某省下的市列表
 * http://127.0.0.1/zantoto/rest/web/v1/area-codes?parent_id=420000
 *
 * 得到当前访客所在城市
 * http://127.0.0.1/zantoto/rest/web/v1/area-codes/get-current-area-code
 *
 */
class AreaCodeController extends ActiveController
{
    public $modelClass = 'common\models\AreaCode';

    public $searchModelClass = 'common\models\AreaCodeSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['view'], $actions['delete']);

        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $parent_id = Yii::$app->request->get('parent_id');
        $data = Yii::$app->db->cache(function ($db) use ($parent_id) {
            return AreaCode::find()->select(['id', 'name'])->where(['parent_id' => $parent_id])->asArray()->all();
        }, YII_DEBUG ? 1 : 24 * 3600);
        // 对于有的区县, 如果没有下级, 就把自己当下级
        if (empty($data)) {
            return AreaCode::find()->select(['id', 'name'])->where(['id' => $parent_id])->asArray()->all();
        }
        return $data;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
    }

    public function optional()
    {
        return [
            'index',
            'get-current-area-code',
            'get-province-agent-stat',
            'get-city-code',
        ];
    }

    public function actionGetCurrentAreaCode()
    {
        return Util::getCurrentAreaCode();
    }

    public function actionGetProvinceAgentStat()
    {
        return 1000;
    }

    /**
     *
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function actionGetCityCode()
    {
        $area_code = Yii::$app->request->get('area_code');
        $id = substr($area_code, 0, 4) . '00';
        $model = AreaCode::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('无效的行政区划代码'); // 6位数
        }
        return $model;
    }


}