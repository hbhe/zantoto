<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use common\wosotech\helper\Util;
use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 * This is the model class for table "{{%order_sku}}".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $product_id
 * @property integer $member_id
 * @property integer $buyer_id
 * @property integer $sku_id
 * @property string $sku_code
 * @property string $title
 * @property string $sub_title
 * @property string $main_image
 * @property string $main_image_thumb
 * @property string $option_value_names
 * @property string $price
 * @property string $image
 * @property integer $quantity
 * @property integer $amount
 * @property integer $revenue_amount
 * @property integer $award_fish
 * @property integer $award_coupon
 * @property integer $status
 * @property integer $is_rated
 * @property string $created_at
 * @property string $updated_at
 * @property string $rate_time
 *
 * @property Member $member
 * @property Member $buyer
 * @property Product $product
 * @property Sku $sku
 * @property Order $order
 * @property OrderSkuRefund $orderSkuRefund
 * @property Rate $rate
 */
class OrderSku extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_sku}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'buyer_id', 'quantity', 'status', 'sku_id', 'is_rated'], 'integer'],
            [['member_id', 'buyer_id', 'quantity', 'status', 'sku_id'], 'default', 'value' => 0],
            [['price', 'amount',], 'number'],
            [['price', 'amount',], 'default', 'value' => 0],
            [['created_at', 'updated_at', 'rate_time'], 'safe'],
            [['order_id', 'product_id', 'sku_code', 'title', 'sub_title'], 'string', 'max' => 64],
            [['option_value_names'], 'string', 'max' => 500],

            [['main_image_type', 'main_image', 'main_image_thumb'], 'safe'],
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
            'order_id' => '??????ID',
            'product_id' => '??????ID',
            'member_id' => '??????ID',
            'buyer_id' => '??????ID',
            'sku_id' => 'SKU ID',
            'sku_code' => 'SKU??????',
            'title' => '????????????',
            'sub_title' => '?????????',
            'main_image' => '??????',
            'main_image_thumb' => '?????????',
            'imageUrl' => '????????????', // ??????
            'thumbImageUrl' => '????????????', // ?????????
            'option_value_names' => '??????????????????',
            'price' => '???????????????',
            'image' => 'SKU??????',
            'quantity' => '??????',
            'amount' => '??????',
            'status' => '??????????????????',  // ???OrderSkuRefund??????status??????
            'statusString' => '??????????????????',
            'created_at' => '????????????',
            'updated_at' => '????????????',
            'is_rated' => '???????????????',
            'rate_time' => '????????????',
        ];
    }

    /**
     * @inheritdoc
     * @return OrderSkuQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderSkuQuery(get_called_class());
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

    public function getSku()
    {
        return $this->hasOne(Sku::className(), ['id' => 'sku_id']);
    }

    public function getRate()
    {
        return $this->hasOne(Rate::className(), ['order_sku_id' => 'id']);
    }

    public function getOrderSkuRefund()
    {
        return $this->hasOne(OrderSkuRefund::className(), ['order_sku_id' => 'id']);
    }

    public function getStatusString()
    {
        return ArrayHelper::getValue(OrderSkuRefund::getStatusOptions(), $this->status);
    }

    public function getCanRefund()
    {
    }

    public function afterDelete()
    {
        if ($this->rate) {
            $this->rate->delete();
        }
        parent::afterDelete();
    }

    public function transactions()
    {
        return [Model::SCENARIO_DEFAULT => self::OP_ALL];
    }

    public function beforeSave($insert)
    {
        $this->product_id = ArrayHelper::getValue($this, 'sku.product.id');
        $this->title = ArrayHelper::getValue($this, 'sku.product.title');
        $this->sub_title = ArrayHelper::getValue($this, 'sku.product.sub_title');
        $this->price = ArrayHelper::getValue($this, 'sku.price');
        $this->amount = $this->price * $this->quantity;
        $this->option_value_names = ArrayHelper::getValue($this, 'sku.option_value_names');
        $this->main_image_type = ArrayHelper::getValue($this, 'product.mainImage.type');
        if (empty($this->sku_id)) {
            $this->sku_id = ArrayHelper::getValue($this, 'sku.id');
        }
        if (empty($this->sku_code)) {
            $this->sku_code = ArrayHelper::getValue($this, 'sku.sku_code');
        }

        $file = Yii::getAlias("@storage/web/images/order-sku/") . $this->sku_code . Util::getFilenameExt($this->product->mainImage->path);
        if (!file_exists($file)) {
            copy($this->product->mainImageUrl, $file);
        }
        $this->main_image = Yii::getAlias("@storageUrl/images/order-sku/") . $this->sku_code . Util::getFilenameExt($this->product->mainImage->path);

        $file = Yii::getAlias("@storage/web/images/order-sku/thumb/") . $this->sku_code . Util::getFilenameExt($this->product->mainImage->path);
        if (!file_exists($file)) {
            copy($this->product->mainThumbImageUrl, $file);
        }
        $this->main_image_thumb = Yii::getAlias("@storageUrl/images/order-sku/thumb/") . $this->sku_code . Util::getFilenameExt($this->product->mainImage->path);

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // ????????????????????????
            // if ($this->sku->quantity < $this->quantity) {
            //    throw new HttpException(400, '?????????????????????');
            // }
            // $this->sku->updateCounters(['quantity' => -$this->quantity]);
        }

        if (isset($changedAttributes['is_rated']) && $changedAttributes['is_rated'] != $this->is_rated) {
            // ???????????????????????????orderSku, ??????????????????, ??????????????????????????????
            $count = OrderSku::find()->where(['order_id' => $this->order_id, 'is_rated' => 0])->count();
            if ($count == 0) { // ?????????????????????
                $this->order->status = Order::STATUS_RATED;
                if (!$this->order->save()) {
                    Yii::error([__METHOD__, __LINE__, $this->order->errors]);
                    throw new HttpException(400, '????????????');
                }
            }
        }

        // ?????????????????????????????????????????????, ????????????????????????; ?????????????????????????????????, ????????????????????????
        if (isset($changedAttributes['status']) && $changedAttributes['status'] != $this->status) {
            $count = OrderSku::find()->where(['order_id' => $this->order_id, 'status' => OrderSkuRefund::STATUS_NONE])->count();
            if ($count == 0) { // ?????????????????????, ????????????????????????
                $count1 = OrderSku::find()->where(['order_id' => $this->order_id, 'status' => [OrderSkuRefund::STATUS_WAIT, OrderSkuRefund::STATUS_ACCEPT]])->count();
                // ??????????????????????????????????????????, ????????????????????????
                $this->order->status = $count1 == 0 ? Order::STATUS_REFUNDED_OK : Order::STATUS_REFUNDING;
                if (!$this->order->save()) {
                    Yii::error([__METHOD__, __LINE__, $this->order->errors]);
                    throw new HttpException(400, '????????????');
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function getImageUrl()
    {
        return $this->main_image;
    }

    public function getThumbImageUrl()
    {
        return $this->main_image_thumb;
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['imageUrl'] = 'imageUrl';
        $fields['thumbImageUrl'] = 'thumbImageUrl';
        unset($fields['main_image']);
        unset($fields['main_image_thumb']);
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'buyer';
        $fields[] = 'orderSkuRefund';
        return $fields;
    }

}
