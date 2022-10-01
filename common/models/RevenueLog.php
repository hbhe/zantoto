<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 * This is the model class for table "{{%revenue_log}}".
 *
 * @property string $id
 * @property integer $member_id
 * @property integer $order_sku_id
 * @property integer $kind
 * @property integer $status
 * @property integer $cashout_status
 * @property integer $pay_type
 * @property string $pay_info
 * @property string $reason
 * @property string $title
 * @property integer $amount
 * @property integer $fee
 * @property string $memo
 * @property string $order_amount
 * @property string $order_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $order_time
 * @property Member $member
 * @property string $payTypeString
 * @property string $cashoutStatusString
 *
 * @property Member $member
 * @property Order $order
 * @property OrderSku $orderSku
 */
class RevenueLog extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%revenue_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'kind', 'status', 'pay_type', 'cashout_status', 'order_sku_id'], 'integer'],
            [['member_id', 'kind', 'pay_type', 'cashout_status', 'order_sku_id'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_CLEARED],
            [['amount', 'order_amount', 'fee'], 'number'],
            [['amount', 'order_amount', 'fee'], 'default', 'value' => 0],
            [['created_at', 'updated_at', 'order_time', 'order_id'], 'safe'],
            [['title', 'memo'], 'string', 'max' => 128],
            [['pay_info', 'reason'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '会员ID',
            'kind' => '类型',
            'kindString' => '类型',
            'title' => '标题',
            'amount' => '金额',
            'memo' => '备注',
            'order_amount' => '成交金额',
            'order_id' => '订单ID',
            'order_sku_id' => '订单SKU',
            'status' => '状态', // 结算状态
            'statusString' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'order_time' => '发生时间',
            'pay_type' => '提现账户类型',
            'payTypeString' => '提现账户类型',
            'pay_info' => '提现账户',
            'fee' => '手续费',
            'reason' => '理由',
            'mobile' => '手机',
            'name' => '姓名',
            'cashout_status' => '提现状态',
            'cashoutStatusString' => '提现状态',
        ];
    }

    public static function find()
    {
        return new RevenueLogQuery(get_called_class());
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderSku()
    {
        return $this->hasOne(OrderSku::className(), ['id' => 'order_sku_id']);
    }

    const KIND_PAY = 0;
    const KIND_FROM_ORDER = 1;
    const KIND_CASHOUT = 2;
    const KIND_REFUND = 3;
    const KIND_COMMISSION_SALE = 20;
    const KIND_COMMISSION_NEW_STAR = 21;
    const KIND_COMMISSION_CHILD_CASHOUT = 22;

    public static function getKindArray()
    {
        return [
            self::KIND_PAY => '余额支付',
            self::KIND_FROM_ORDER => '订单余额收入',
            self::KIND_CASHOUT => '余额提现',
            self::KIND_COMMISSION_SALE => '销售奖励', // 下级买单
            self::KIND_COMMISSION_NEW_STAR => '推荐奖励', // 发展下级
            self::KIND_COMMISSION_CHILD_CASHOUT => '提现奖励', // 下级提现
        ];
    }

    public function getKindString()
    {
        return ArrayHelper::getValue(self::getKindArray(), $this->kind);
    }

    const CASHOUT_STATUS_WAIT = 0;
    const CASHOUT_STATUS_OK = 1;
    const CASHOUT_STATUS_REFUSE = 2;
    const CASHOUT_STATUS_REVERSE = 3;

    public static function getCashoutStatus()
    {
        return [
            self::CASHOUT_STATUS_WAIT => '提现待处理',
            self::CASHOUT_STATUS_OK => '提现成功',
            self::CASHOUT_STATUS_REFUSE => '提现已驳回',
            self::CASHOUT_STATUS_REVERSE => '提现驳回退款',
        ];
    }

    static public function getCashoutStatus1String()
    {
        return [
            self::CASHOUT_STATUS_OK => '提现成功',
            self::CASHOUT_STATUS_REFUSE => '提现已驳回',
            self::CASHOUT_STATUS_REVERSE => '提现驳回退款',
        ];
    }

    public function getCashoutStatusString()
    {
        return ArrayHelper::getValue(self::getCashoutStatus(), $this->cashout_status);
    }

    public function isCommission()
    {
        return in_array($this->kind, [RevenueLog::KIND_COMMISSION_CHILD_CASHOUT, RevenueLog::KIND_COMMISSION_NEW_STAR, RevenueLog::KIND_COMMISSION_SALE]);
    }

    const STATUS_UNRATED = 1;
    const STATUS_WAIT_CLEAR = 2;
    const STATUS_CLEARED = 3;
    const STATUS_INVALID = 9;

    public static function getStatusArray()
    {
        return [
            self::STATUS_CLEARED => '已结算',
            self::STATUS_UNRATED => '未评价',
            self::STATUS_WAIT_CLEAR => '待结算', // 在结算日, 系统会批量将待结算状态改为已结算, 此时用户的余额才增加
            self::STATUS_INVALID => '无效',
        ];
    }

    public function getStatusString()
    {
        return ArrayHelper::getValue(self::getStatusArray(), $this->status);
    }

    public function transactions()
    {
        return [Model::SCENARIO_DEFAULT => self::OP_ALL];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if ($this->kind == self::KIND_CASHOUT) {
                if ($this->pay_type == self::PAY_BANKCARD) {
                    $this->pay_info = "{$this->member->card_bank}, {$this->member->card_branch}, {$this->member->card_name}, {$this->member->card_id}";
                }
                if ($this->pay_type == self::PAY_ALIPAY) {
                    $this->pay_info = "Alipay number:{$this->member->alipay_id}, Name:{$this->member->alipay_name}";
                }
                $this->title = empty($this->title) ? "{$this->kindString}-{$this->cashoutStatusString}" : $this->title;
                $this->memo = $this->payTypeString;
                $this->order_time = date('Y-m-d H:i:s');
                $this->fee = $this->amount * Yii::$app->ks->get('cashout.fee_ratio', 0);
                if (empty($this->cashout_status)) {
                    if (Yii::$app->ks->get('cashout.need_audit', 1) && abs($this->amount) > Yii::$app->ks->get('cashout.audit_threshold', 50)) {
                        $this->cashout_status = self::CASHOUT_STATUS_WAIT;
                    } else {
                        $this->cashout_status = self::CASHOUT_STATUS_WAIT;
                    }
                }
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (isset($changedAttributes['status']) && $changedAttributes['status'] != $this->status) {
            // 状态变为结算时, 用户余额增加
            if ($this->status == self::STATUS_CLEARED) {
                $this->member->updateCounters(['balance_revenue' => $this->amount + $this->fee]);
                if ($this->member->balance_revenue < 0) {
                    Yii::error([__METHOD__, __LINE__, $this->member->id, $this->member->balance_revenue]);
                    throw new HttpException(400, '余额不足!!');
                }
            }
        }

        if (isset($changedAttributes['cashout_status']) && $changedAttributes['cashout_status'] != $this->cashout_status) {
            if ($this->cashout_status == self::CASHOUT_STATUS_REFUSE) {
                $model = new RevenueLog(); // 拒绝提现之后, 重新插入一条新记录, 金额相反
                $model->attributes = [
                    'member_id' => $this->member_id,
                    'amount' => -$this->amount,
                    'pay_type' => $this->pay_type,
                    'kind' => RevenueLog::KIND_CASHOUT,
                    'cashout_status' => self::CASHOUT_STATUS_REVERSE,
                    'reason' => "{$this->reason}, $this->id",
                ];
                if (!$model->save(false)) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                    throw new HttpException(400, '处理失败');
                }
            }
        }
    }

    /**
     * 每月10号结帐, 即将待结算标志改为已结算, 用户的余额会增加
     */
    static public function clear()
    {
        $query = RevenueLog::find()
            ->andWhere(['status' => RevenueLog::STATUS_WAIT_CLEAR]);
        foreach ($query->each() as $model) {
            $model->status = RevenueLog::STATUS_CLEARED;
            try {
                if (!$model->save()) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                }
            } catch (Exception $e) {

            }
        }
    }

    static public function getAmountStat($member_id, $kind)
    {
        $sum = RevenueLog::find()
            ->andWhere(['member_id' => $member_id])
            ->andWhere(['kind' => $kind])
            ->sum('amount');
        return empty($sum) ? 0 : $sum;
    }

    const PAY_BANKCARD = 0;
    const PAY_ALIPAY = 1;
    const PAY_WEIXIN = 2;

    static public function getPayTypeArray()
    {
        return [
            self::PAY_BANKCARD => '银行卡',
            self::PAY_ALIPAY => '支付宝',
            //self::PAY_WEIXIN => '微信收款码',
        ];
    }

    public function getPayTypeString()
    {
        return ArrayHelper::getValue(self::getPayTypeArray(), $this->pay_type);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['kindString'] = 'kindString';
        unset($fields['pay_info']);
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'order';
        return $fields;
    }
}