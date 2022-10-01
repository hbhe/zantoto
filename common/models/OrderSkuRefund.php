<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use mohorev\file\UploadImageBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 * This is the model class for table "{{%order_sku_refund}}".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $product_id
 * @property integer $member_id
 * @property integer $buyer_id
 * @property string $mobile
 * @property string $nickname
 * @property integer $order_sku_id
 * @property integer $need_ship
 * @property string $refund_reason
 * @property string $refund_detail
 * @property string $refund_amount
 * @property string $refund_coupon
 * @property integer $handled_by
 * @property string $handled_time
 * @property string $handled_memo
 * @property string $shipping_address
 * @property string $shipping_zipcode
 * @property string $shipping_name
 * @property string $shipping_mobile
 * @property integer $shipping_by
 * @property string $shipping_time
 * @property string $shipping_memo
 * @property string $express_company
 * @property string $express_code
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $image1
 * @property string $image2
 * @property string $image3
 *
 * @property Member $member
 * @property Member $buyer
 * @property Product $product
 * @property Sku $sku
 * @property Order $order
 * @property OrderSku $orderSku
 */
class OrderSkuRefund extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_sku_refund}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'buyer_id', 'order_sku_id', 'need_ship', 'handled_by', 'shipping_by', 'status'], 'integer'],
            [['member_id', 'buyer_id', 'order_sku_id', 'need_ship', 'handled_by', 'shipping_by'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_WAIT],
            [['refund_amount', 'refund_coupon'], 'number'],
            [['refund_amount', 'refund_coupon'], 'default', 'value' => 0],
            [['handled_time', 'shipping_time', 'created_at', 'updated_at'], 'safe'],
            [['order_id', 'product_id', 'nickname', 'refund_reason', 'shipping_memo'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 16],
            [['refund_detail', 'handled_memo'], 'string', 'max' => 500],
            [['shipping_address'], 'string', 'max' => 255],
            [['shipping_zipcode', 'shipping_name', 'shipping_mobile', 'express_company', 'express_code'], 'string', 'max' => 32],
            [['order_sku_id'], 'unique'],
            [['order_sku_id'], 'exist', 'targetRelation' => 'orderSku'],

            [['image1', 'image2', 'image3',], 'image',], // 'on' => ['insert', 'update'] 'extensions' => 'jpg, jpeg, gif, png',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],

            'image1' => [
                'class' => UploadImageBehavior::class,
                'generateNewName' => function ($file) {
                    return md5(uniqid() . $file->name . rand(1, 1000)) . '.' . $file->extension;
                }, // form中多文件上传时用这个保证唯一性, 插件中的默然函数不行
                'instanceByName' => true, // 因为REST中想直接使用pay_filename=xxx.jpg, 不想用Trade[pay_filename]=xxx.jpg
                'attribute' => 'image1',
                'scenarios' => [self::SCENARIO_DEFAULT],
                //'placeholder' => '@storage/web/images/avatar.png',
                'path' => '@storage/web/images/refund', // 定义文件存放的目录
                'url' => '@storageUrl/images/refund',
                //'url' => '@backendUrl/img/share/{id}',
                'thumbs' => [ // 定义各种size的thumbs
                    'thumb' => ['width' => 100, 'height' => 100, 'quality' => 90],
                    //'preview' => ['width' => 200, 'height' => 200], // 'bg_color' => '000'
                ],
            ],

            'image2' => [
                'class' => UploadImageBehavior::class,
                'generateNewName' => function ($file) {
                    return md5(uniqid() . $file->name . rand(1, 1000)) . '.' . $file->extension;
                }, // form中多文件上传时用这个保证唯一性, 插件中的默然函数不行
                'instanceByName' => true, // 因为REST中想直接使用pay_filename=xxx.jpg, 不想用Trade[pay_filename]=xxx.jpg
                'attribute' => 'image2',
                //'scenarios' => ['insert', 'update'],
                'scenarios' => [self::SCENARIO_DEFAULT],
                //'placeholder' => '@storage/web/images/avatar.png',
                'path' => '@storage/web/images/refund', // 定义文件存放的目录
                'url' => '@storageUrl/images/refund',
                //'url' => '@backendUrl/img/share/{id}',
                'thumbs' => [ // 定义各种size的thumbs
                    'thumb' => ['width' => 100, 'height' => 100, 'quality' => 90],
                ],
            ],

            'image3' => [
                'class' => UploadImageBehavior::class,
                'generateNewName' => function ($file) {
                    return md5(uniqid() . $file->name . rand(1, 1000)) . '.' . $file->extension;
                }, // form中多文件上传时用这个保证唯一性, 插件中的默然函数不行
                'instanceByName' => true, // 因为REST中想直接使用pay_filename=xxx.jpg, 不想用Trade[pay_filename]=xxx.jpg
                'attribute' => 'image3',
                'scenarios' => [self::SCENARIO_DEFAULT],
                //'placeholder' => '@storage/web/images/avatar.png',
                'path' => '@storage/web/images/refund', // 定义文件存放的目录
                'url' => '@storageUrl/images/refund',
                //'url' => '@backendUrl/img/share/{id}',
                'thumbs' => [ // 定义各种size的thumbs
                    'thumb' => ['width' => 100, 'height' => 100, 'quality' => 90],
                ],
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
            'order_id' => '订单ID',
            'product_id' => '商品ID',
            'member_id' => '卖家ID',
            'buyer_id' => '买家ID',
            'mobile' => '买家手机号',
            'nickname' => '买家昵称',
            'order_sku_id' => 'ID',
            'need_ship' => '是否需发货',
            'refund_reason' => '退款原因',
            'refund_detail' => '问题描述',
            'refund_amount' => '退款金额',
            'refund_coupon' => '退款礼券',
            'handled_by' => '处理人',
            'handled_time' => '处理时间',
            'handled_memo' => '处理备注',
            'shipping_address' => '收货地址',
            'shipping_zipcode' => '邮编',
            'shipping_name' => '收货人',
            'shipping_mobile' => '收货人电话',
            'shipping_by' => '收货人',
            'shipping_time' => '收货时间',
            'shipping_memo' => '收货备注',
            'express_company' => '物流公司',
            'express_code' => '运单号',
            'status' => '商品退货状态', // 与OrderSku中的status同步
            'statusString' => '退货状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'image1' => '凭证图片1',
            'image2' => '凭证图片2',
            'image3' => '凭证图片3',
            'image1Url' => '凭证图片1',
            'image1ThumbUrl' => '凭证图片1',
            'image2Url' => '凭证图片2',
            'image2ThumbUrl' => '凭证图片2',
            'image3Url' => '凭证图片3',
            'image3ThumbUrl' => '凭证图片3',
        ];
    }

    public function transactions()
    {
        return [Model::SCENARIO_DEFAULT => self::OP_ALL];
    }

    /**
     * @inheritdoc
     * @return OrderSkuRefundQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderSkuRefundQuery(get_called_class());
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getBuyer()
    {
        return $this->hasOne(Member::className(), ['id' => 'buyer_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getOrderSku()
    {
        return $this->hasOne(OrderSku::className(), ['id' => 'order_sku_id']);
    }

    public function getImage1Url()
    {
        return $this->getUploadUrl('image1');
    }

    public function getImage1ThumbUrl()
    {
        return $this->getThumbUploadUrl('image1', 'thumb');
    }

    public function getImage2Url()
    {
        return $this->getUploadUrl('image2');
    }

    public function getImage2ThumbUrl()
    {
        return $this->getThumbUploadUrl('image2', 'thumb');
    }

    public function getImage3Url()
    {
        return $this->getUploadUrl('image3');
    }

    public function getImage3ThumbUrl()
    {
        return $this->getThumbUploadUrl('image3', 'thumb');
    }

    const STATUS_NONE = 0;
    const STATUS_WAIT = 1;
    const STATUS_ACCEPT = 2;
    const STATUS_CONFIRM = 3;
    const STATUS_REFUSED = 4;

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NONE => '无退货',
            self::STATUS_WAIT => '待处理',  // 待卖家同意
            self::STATUS_ACCEPT => '退货中', // 卖家同意退货
            self::STATUS_CONFIRM => '退货已完成', // 买家确认退货完成
            self::STATUS_REFUSED => '已拒绝退货', // 卖家拒绝退货
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusString()
    {
        return ArrayHelper::getValue(self::getStatusOptions(), $this->status);
    }

    public function beforeSave($insert)
    {
        if ($this->member_id = Member::ROOT_ID) {
            $this->shipping_address = '广东省深圳市南山区科兴科学园';
            $this->shipping_name = '亿鲸服务有限公司';
            $this->shipping_mobile = '18509088909';
        }

        if ($insert) {
            $this->refund_amount = ArrayHelper::getValue($this, 'orderSku.amount');
        }
        $this->member_id = ArrayHelper::getValue($this, 'orderSku.member_id');
        $this->order_id = ArrayHelper::getValue($this, 'orderSku.order_id');
        $this->product_id = ArrayHelper::getValue($this, 'orderSku.product_id');
        $this->mobile = ArrayHelper::getValue($this, 'buyer.mobile');
        $this->nickname = ArrayHelper::getValue($this, 'buyer.nickname');
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->orderSku) {
            $this->orderSku->status = $this->status;
            if (!$this->orderSku->save()) {
                Yii::error([__METHOD__, __LINE__, $this->orderSku->errors]);
                throw new HttpException(400, '操作失败');
            }
        }
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['image1Url'] = 'image1Url';
        $fields['image2Url'] = 'image2Url';
        $fields['image3Url'] = 'image3Url';
        $fields['statusString'] = 'statusString';
        unset($fields['image1']);
        unset($fields['image2']);
        unset($fields['image3']);
        return $fields;
    }


}
