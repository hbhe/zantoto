<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property string $tid
 * @property integer $member_id
 * @property integer $buyer_id
 * @property integer $is_platform
 * @property string $mobile
 * @property string $nickname
 * @property string $total_amount
 * @property integer $quantity
 * @property string $shipping_fee
 * @property string $pay_amount
 * @property integer $pay_method
 * @property string $pay_time
 * @property integer $revenue_status
 * @property string $shipping_area_parent_id
 * @property string $shipping_area_id
 * @property string $shipping_district_id
 * @property string $shipping_address
 * @property string $shipping_zipcode
 * @property string $shipping_name
 * @property string $shipping_mobile
 * @property string $shipping_time
 * @property string $express_company
 * @property string $express_code
 * @property integer $status
 * @property integer $has_refund
 * @property integer $member_address_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $memo
 * @property string $confirm_time
 * @property string $rate_time
 *
 * @property Member $member
 * @property Member $buyer
 * @property MemberAddress $memberAddress
 * @property OrderSku[] $orderSkus
 */
class Order extends ActiveRecord
{
    public $order_skus;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'buyer_id', 'quantity', 'pay_method', 'status', 'has_refund', 'is_platform', 'member_address_id'], 'integer'], // 'revenue_status',
            [['member_id', 'buyer_id', 'quantity', 'pay_method', 'is_platform', 'has_refund', 'member_address_id'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_AUCTION],
            [['total_amount', 'shipping_fee', 'pay_amount',], 'number'],
            [['total_amount', 'shipping_fee', 'pay_amount',], 'default', 'value' => 0],
            [['pay_time', 'shipping_time', 'created_at', 'updated_at', 'confirm_time', 'rate_time'], 'safe'],
            [['id', 'tid', 'nickname'], 'string', 'max' => 64],
            [['mobile', 'shipping_area_parent_id', 'shipping_area_id', 'shipping_district_id'], 'string', 'max' => 16],
            [['shipping_address'], 'string', 'max' => 255],
            [['shipping_zipcode', 'shipping_name', 'shipping_mobile', 'express_company', 'express_code'], 'string', 'max' => 32],
            [['memo'], 'string', 'max' => 2048],
            [['id'], 'unique'],

            [['order_skus'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'id'
                ],
                'value' => self::generateSid(),
            ],
        ];
    }

    public static function generateSid()
    {
        return uniqid() . sprintf("%02d", rand(0, 99)) . '-o';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单号',
            'tid' => '事务ID',
            'member_id' => '商户ID',
            'buyer_id' => '买家ID',
            'mobile' => '买家手机号',
            'nickname' => '买家昵称',
            'total_amount' => '总金额', // 商品总金额
            'quantity' => '数量', // 商品总件数
            'shipping_fee' => '邮费',
            'pay_amount' => '应付金额', // 应付 = 总金额 - 收券
            'pay_method' => '支付方式',
            'pay_time' => '付款时间',
            'shipping_area_parent_id' => '省份',
            'shipping_area_id' => '地市',
            'shipping_district_id' => '区县',
            'shipping_address' => '收货地址',
            'shipping_zipcode' => '邮编',
            'shipping_name' => '收货人',
            'shipping_mobile' => '收货人电话',
            'shipping_time' => '发货时间',
            'express_company' => '物流公司',
            'express_code' => '运单号',
            'status' => '订单状态', // 当所有商品都是退货情况时, 才变为
            'has_refund' => '有退货', // 暂时未用, 只要有1件商品退货就标为1
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'confirm_time' => '确认收货时间',
            'rate_time' => '评价时间',
            'memo' => '备注',
            'statusString' => '状态',
            'statusForSellerString' => '状态',
            'is_platform' => '是平台订单',
            'parentAreaCodeName' => '省份',
            'areaCodeName' => '地市',
            'districtAreaCodeName' => '区省',
            'payMethodString' => 'PayMethodString',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->is_platform = $this->member_id == Member::ROOT_ID ? 1 : 0;
            $this->mobile = ArrayHelper::getValue($this, 'buyer.mobile');
            $this->nickname = ArrayHelper::getValue($this, 'buyer.nickname');
            $this->shipping_area_parent_id = ArrayHelper::getValue($this, 'memberAddress.area_parent_id', '');
            $this->shipping_area_id = ArrayHelper::getValue($this, 'memberAddress.area_id', '');
            $this->shipping_district_id = ArrayHelper::getValue($this, 'memberAddress.district_id', '');
            $this->shipping_address = ArrayHelper::getValue($this, 'memberAddress.address', '');
            $this->shipping_name = ArrayHelper::getValue($this, 'memberAddress.name', '');
            $this->shipping_mobile = ArrayHelper::getValue($this, 'memberAddress.mobile', '');
        }
        return parent::beforeSave($insert);
    }

    public function transactions()
    {
        return [Model::SCENARIO_DEFAULT => self::OP_ALL];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            return;
        }

        if (isset($changedAttributes['status']) && $changedAttributes['status'] != $this->status) {
            // 付款成功
            if ($this->status == Order::STATUS_PAID) {
                $this->updateAttributes(['pay_time' => date('Y-m-d H:i:s')]); // 付款时间
                // 支付减库存
                foreach ($this->orderSkus as $orderSku) {
                    if ($orderSku->sku) {
                        $orderSku->sku->updateCounters(['quantity' => -$orderSku->quantity]);
                    }
                    if ($orderSku->product) {
                        $orderSku->product->flushQuantity();
                    }
                }
            }

            // 卖家确认发货
            if ($this->status == Order::STATUS_SHIPPED) {
                if (!in_array(ArrayHelper::getValue($changedAttributes, 'status'), [self::STATUS_PAID])) {
                    throw new HttpException(400, "买家尚未确认付款!");
                }
                $this->updateAttributes(['shipping_time' => date('Y-m-d H:i:s')]); // 发货时间
            }

            // 买家确认收货
            if ($this->status == Order::STATUS_CONFIRM) {
                $this->updateAttributes(['confirm_time' => date('Y-m-d H:i:s')]); // 收货时间
            }

            // 关闭交易
            if ($this->status == Order::STATUS_CLOSED) {
            }

            // 买家评价订单
            if ($this->status == Order::STATUS_RATED) {
                $this->updateAttributes(['rate_time' => date('Y-m-d H:i:s')]); // 评价时间
            }
        }
    }

    /**
     * @inheritdoc
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }

    public function payOrderByRevenue()
    {
        $amount = $this->pay_amount;

        $ar = new RevenueLog();
        $ar->attributes = [
            'member_id' => $this->buyer_id,
            'kind' => RevenueLog::KIND_PAY,
            'title' => $this->orderSkus[0]->title,
            'amount' => -$amount,
            'memo' => "订单编号:{$this->id}",
            'order_id' => $this->id,
            'order_amount' => abs($amount),
            'order_time' => date('Y-m-d H:i:s'),
        ];
        if (!$ar->save()) {
            Yii::error([__METHOD__, __LINE__, $ar->errors]);
            throw new HttpException(400, "保存失败!");
        }

        // 卖家增加余额
        $ar = new RevenueLog();
        $ar->attributes = [
            'member_id' => $this->member_id,
            'kind' => RevenueLog::KIND_FROM_ORDER,
            'title' => $this->orderSkus[0]->title,
            'amount' => $amount,
            'memo' => "订单编号:{$this->id}",
            'order_id' => $this->id,
            'order_amount' => abs($amount),
            'order_time' => date('Y-m-d H:i:s'),
        ];
        if (!$ar->save()) {
            Yii::error([__METHOD__, __LINE__, $ar->errors]);
            throw new HttpException(400, "保存失败!");
        }

        // 订单标为已支付
        $this->status = Order::STATUS_PAID;
        $this->pay_method = Order::PAY_METHOD_BALANCE;
        if (!$this->save()) {
            Yii::error([__METHOD__, __LINE__, $this->errors]);
            throw new HttpException(500, "保存失败!");
        }
        return $this;
    }

    const STATUS_AUCTION = 1;
    const STATUS_PAID = 2;
    const STATUS_SHIPPED = 3;
    const STATUS_CONFIRM = 4;
    const STATUS_CLOSED = 5;
    const STATUS_RATED = 6;
    const STATUS_REFUNDED_OK = 7;
    const STATUS_REFUNDING = 8;

    public static function getStatusOptions()
    {
        return [
            self::STATUS_AUCTION => '待付款',
            self::STATUS_PAID => '待发货',
            self::STATUS_SHIPPED => '待收货',
            self::STATUS_CONFIRM => '待评价',
            self::STATUS_CLOSED => '已关闭', // 已付款取消交易时, 未付款取消交易时
            self::STATUS_RATED => '交易完成',
            self::STATUS_REFUNDED_OK => '退款完成', // 当全部商品退款或退货成功时就变成此状态
            self::STATUS_REFUNDING => '退款中', // 当全部商品都申请退款时就变成此状态
        ];
    }

    public static function getStatusOptionsForSeller()
    {
        return [
            self::STATUS_AUCTION => '待付款',
            self::STATUS_PAID => '待发货(已付款)',
            self::STATUS_SHIPPED => '待收货(已发货)',
            self::STATUS_CONFIRM => '待评价(已收货)',
            self::STATUS_CLOSED => '已关闭',  // 已付款取消交易时, 未付款取消交易时
            self::STATUS_RATED => '交易完成(已评价)',
            self::STATUS_REFUNDED_OK => '退款完成', // 当全部商品退款或退货成功时就变成此状态
            self::STATUS_REFUNDING => '退款中', // 当全部商品都申请退款时就变成此状态
        ];
    }

    const PAY_METHOD_BALANCE = 0;
    const PAY_METHOD_ALIPAY = 1;
    const PAY_METHOD_WECHAT = 2;
    const PAY_METHOD_BANK = 3;

    public static function getPayMethodOptions()
    {
        return [
            self::PAY_METHOD_ALIPAY => '支付宝',
            self::PAY_METHOD_WECHAT => '微信',
            self::PAY_METHOD_BANK => '银联',
            self::PAY_METHOD_BALANCE => '余额',
        ];
    }

    public function getPayMethodString()
    {
        return ArrayHelper::getValue(self::getPayMethodOptions(), $this->pay_method);
    }

    /**
     * @return mixed
     */
    public function getStatusString()
    {
        return ArrayHelper::getValue(self::getStatusOptions(), $this->status);
    }

    public function getStatusForSellerString()
    {
        return ArrayHelper::getValue(self::getStatusOptionsForSeller(), $this->status);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getBuyer()
    {
        return $this->hasOne(Member::className(), ['id' => 'buyer_id']);
    }

    public function getOrderSkus()
    {
        return $this->hasMany(OrderSku::className(), ['order_id' => 'id']);
    }

    public function afterDelete()
    {
        foreach ($this->orderSkus as $model) {
            $model->delete();
        }
        parent::afterDelete();
    }

    public function getMemberAddress()
    {
        return $this->hasOne(MemberAddress::className(), ['id' => 'member_address_id']);
    }

    public function getAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'shipping_area_id']);
    }

    public function getParentAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'shipping_area_parent_id']);
    }

    public function getDistrictAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'shipping_district_id']);
    }

    public function getAreaCodeName()
    {
        return ArrayHelper::getValue($this, 'areaCode.name');
    }

    public function getParentAreaCodeName()
    {
        return ArrayHelper::getValue($this, 'parentAreaCode.name');
    }

    public function getDistrictAreaCodeName()
    {
        return ArrayHelper::getValue($this, 'districtAreaCode.name');
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['statusString'] = 'statusString';
        $fields['parentAreaCodeName'] = 'parentAreaCodeName';
        $fields['areaCodeName'] = 'areaCodeName';
        $fields['districtAreaCodeName'] = 'districtAreaCodeName';
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'orderSkus';
        return $fields;
    }

}
