<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use common\models\Banner;
use common\models\Honor;
use common\models\MarketPrice;
use common\models\Member;
use common\models\Need;
use common\models\Order;
use common\models\PowerLog;
use common\models\ProfitLog;
use common\models\Suborder;
use common\models\User;
use rest\controllers\ActiveController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * Class DashboardController
 * @package rest\modules\v1\controllers
 *
 */
class DashboardController extends ActiveController
{
    public $modelClass = 'common\models\Order';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['create'], $actions['update'], $actions['delete'], $actions['view']);

        return $actions;
    }

    public function actionGetSiteSettings()
    {
        $arr['power_to_fish_ratio'] = PowerLog::POWER_TO_FISH_RATIO;
        $arr['max_wait_pay_minutes'] = Yii::$app->ks->get('order.max_wait_pay_minutes', 120);
        $arr['max_wait_confirm_receive'] = Yii::$app->ks->get('order.max_wait_confirm_receive', 15);
        $arr['max_wait_complain'] = Yii::$app->ks->get('order.max_wait_complain', 7);
        $arr['max_wait_rate'] = Yii::$app->ks->get('order.max_wait_rate', 7);
        $arr['open_star_member_money'] = Yii::$app->ks->get('revenue.open_star_member_money', 368);
        $arr['bonus_per_star_member'] = Yii::$app->ks->get('revenue.bonus_per_star_member', 99);
        $arr['training_bonus_ratio'] = Yii::$app->ks->get('revenue.training_bonus_ratio', 0.04);
        return $arr;
    }

    public function optional()
    {
        return [
            'get-stat',
            'lookup',
        ];
    }

}
