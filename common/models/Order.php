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
            'id' => '?????????',
            'tid' => '??????ID',
            'member_id' => '??????ID',
            'buyer_id' => '??????ID',
            'mobile' => '???????????????',
            'nickname' => '????????????',
            'total_amount' => '?????????', // ???????????????
            'quantity' => '??????', // ???????????????
            'shipping_fee' => '??????',
            'pay_amount' => '????????????', // ?????? = ????????? - ??????
            'pay_method' => '????????????',
            'pay_time' => '????????????',
            'shipping_area_parent_id' => '??????',
            'shipping_area_id' => '??????',
            'shipping_district_id' => '??????',
            'shipping_address' => '????????????',
            'shipping_zipcode' => '??????',
            'shipping_name' => '?????????',
            'shipping_mobile' => '???????????????',
            'shipping_time' => '????????????',
            'express_company' => '????????????',
            'express_code' => '?????????',
            'status' => '????????????', // ????????????????????????????????????, ?????????
            'has_refund' => '?????????', // ????????????, ?????????1????????????????????????1
            'created_at' => '????????????',
            'updated_at' => '????????????',
            'confirm_time' => '??????????????????',
            'rate_time' => '????????????',
            'memo' => '??????',
            'statusString' => '??????',
            'statusForSellerString' => '??????',
            'is_platform' => '???????????????',
            'parentAreaCodeName' => '??????',
            'areaCodeName' => '??????',
            'districtAreaCodeName' => '??????',
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
            // ????????????
            if ($this->status == Order::STATUS_PAID) {
                $this->updateAttributes(['pay_time' => date('Y-m-d H:i:s')]); // ????????????
                // ???????????????
                foreach ($this->orderSkus as $orderSku) {
                    if ($orderSku->sku) {
                        $orderSku->sku->updateCounters(['quantity' => -$orderSku->quantity]);
                    }
                    if ($orderSku->product) {
                        $orderSku->product->flushQuantity();
                    }
                }
            }

            // ??????????????????
            if ($this->status == Order::STATUS_SHIPPED) {
                if (!in_array(ArrayHelper::getValue($changedAttributes, 'status'), [self::STATUS_PAID])) {
                    throw new HttpException(400, "????????????????????????!");
                }
                $this->updateAttributes(['shipping_time' => date('Y-m-d H:i:s')]); // ????????????
            }

            // ??????????????????
            if ($this->status == Order::STATUS_CONFIRM) {
                $this->updateAttributes(['confirm_time' => date('Y-m-d H:i:s')]); // ????????????
            }

            // ????????????
            if ($this->status == Order::STATUS_CLOSED) {
            }

            // ??????????????????
            if ($this->status == Order::STATUS_RATED) {
                $this->updateAttributes(['rate_time' => date('Y-m-d H:i:s')]); // ????????????
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
            'memo' => "????????????:{$this->id}",
            'order_id' => $this->id,
            'order_amount' => abs($amount),
            'order_time' => date('Y-m-d H:i:s'),
        ];
        if (!$ar->save()) {
            Yii::error([__METHOD__, __LINE__, $ar->errors]);
            throw new HttpException(400, "????????????!");
        }

        // ??????????????????
        $ar = new RevenueLog();
        $ar->attributes = [
            'member_id' => $this->member_id,
            'kind' => RevenueLog::KIND_FROM_ORDER,
            'title' => $this->orderSkus[0]->title,
            'amount' => $amount,
            'memo' => "????????????:{$this->id}",
            'order_id' => $this->id,
            'order_amount' => abs($amount),
            'order_time' => date('Y-m-d H:i:s'),
        ];
        if (!$ar->save()) {
            Yii::error([__METHOD__, __LINE__, $ar->errors]);
            throw new HttpException(400, "????????????!");
        }

        // ?????????????????????
        $this->status = Order::STATUS_PAID;
        $this->pay_method = Order::PAY_METHOD_BALANCE;
        if (!$this->save()) {
            Yii::error([__METHOD__, __LINE__, $this->errors]);
            throw new HttpException(500, "????????????!");
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
            self::STATUS_AUCTION => '?????????',
            self::STATUS_PAID => '?????????',
            self::STATUS_SHIPPED => '?????????',
            self::STATUS_CONFIRM => '?????????',
            self::STATUS_CLOSED => '?????????', // ????????????????????????, ????????????????????????
            self::STATUS_RATED => '????????????',
            self::STATUS_REFUNDED_OK => '????????????', // ?????????????????????????????????????????????????????????
            self::STATUS_REFUNDING => '?????????', // ???????????????????????????????????????????????????
        ];
    }

    public static function getStatusOptionsForSeller()
    {
        return [
            self::STATUS_AUCTION => '?????????',
            self::STATUS_PAID => '?????????(?????????)',
            self::STATUS_SHIPPED => '?????????(?????????)',
            self::STATUS_CONFIRM => '?????????(?????????)',
            self::STATUS_CLOSED => '?????????',  // ????????????????????????, ????????????????????????
            self::STATUS_RATED => '????????????(?????????)',
            self::STATUS_REFUNDED_OK => '????????????', // ?????????????????????????????????????????????????????????
            self::STATUS_REFUNDING => '?????????', // ???????????????????????????????????????????????????
        ];
    }

    const PAY_METHOD_BALANCE = 0;
    const PAY_METHOD_ALIPAY = 1;
    const PAY_METHOD_WECHAT = 2;
    const PAY_METHOD_BANK = 3;

    public static function getPayMethodOptions()
    {
        return [
            self::PAY_METHOD_ALIPAY => '?????????',
            self::PAY_METHOD_WECHAT => '??????',
            self::PAY_METHOD_BANK => '??????',
            self::PAY_METHOD_BALANCE => '??????',
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
