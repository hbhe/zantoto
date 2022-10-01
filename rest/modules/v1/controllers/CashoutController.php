<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace rest\modules\v1\controllers;

use rest\controllers\ActiveController;
use common\models\RevenueLog;
use common\models\RevenueLogSearch;
use Yii;
use yii\base\Exception;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class CashoutController
 * @package rest\modules\v1\controllers
 *
 */
class CashoutController extends ActiveController
{
    public $modelClass = 'common\models\RevenueLog';

    public $searchModelClass = 'common\models\RevenueLogSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view'], $actions['index']);
        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $searchModel = new RevenueLogSearch;
        $searchModel->member_id = Yii::$app->user->id;
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function actionCreate()
    {
        $member = Yii::$app->user->identity;

        $amount = Yii::$app->request->post('amount');
        if (empty($amount) || (!is_numeric($amount)) || $amount <= 0) {
            throw new HttpException('400', '无效的金额');
        }
        $limit = Yii::$app->ks->get('cashout.min_amount_per_time', 1);
        if ($amount < $limit) {
            throw new HttpException('400', "单次提现金额最少不低于{$limit}元！");
        }
        $limit = Yii::$app->ks->get('cashout.max_amount_per_time', 1);
        if ($amount > $limit) {
            throw new HttpException('400', "单次提现金额最大不超过{$limit}元！");
        }
        $pay_type = Yii::$app->request->post('pay_type');
        if (!in_array($pay_type, [RevenueLog::PAY_BANKCARD, RevenueLog::PAY_ALIPAY])) {
            throw new HttpException('400', '无效的类型');
        }

        if ($pay_type == RevenueLog::PAY_BANKCARD && empty($member->card_id)) {
            throw new HttpException('400', '未绑定银行卡!');
        }
        if ($pay_type == RevenueLog::PAY_ALIPAY && empty($member->alipay_id)) {
            throw new HttpException('400', '未绑定支付宝!');
        }

        $model = new RevenueLog();
        $model->attributes = [
            'member_id' => $member->id,
            'amount' => -$amount,
            'pay_type' => $pay_type,
            'kind' => RevenueLog::KIND_CASHOUT,
        ];
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
            throw new HttpException(400, '申请提现失败');
        }

        return $model;
    }

    public function actionView($id)
    {
        $member = $this->findModel($id);
        if ($member->member_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can view your own order');
        }
        return $member;
    }

    public function findModel($id)
    {
        if (($member = RevenueLog::find()->where(['id' => $id])->one()) !== null) {
            return $member;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    public function optional()
    {
        return [
        ];
    }

}