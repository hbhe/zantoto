<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use common\wosotech\helper\Util;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\HttpException;

/**
 * This is the model class for table "{{%rate}}".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $member_id
 * @property integer $buyer_id
 * @property string $product_id
 * @property string $product_title
 * @property string $content
 * @property integer $score
 * @property string $ip
 * @property integer $is_anonymous
 * @property integer $is_hidden
 * @property string $nickname
 * @property string $rate_pictures
 * @property integer $status
 * @property integer $sort_order
 * @property integer $order_sku_id
 * @property integer $is_auto
 * @property integer $is_star
 * @property string $created_at
 * @property string $updated_at
 * @property string $avatar_url
 * @property string $main_image
 * @property string $main_image_thumb
 *
 * @property OrderSku $orderSku
 * @property Member $buyer
 * @property Member $member
 * @property Product $product
 * @property Order $order
 * @property Picture[] $pictures
 */
class Rate extends \common\models\ActiveRecord
{
    public $rate_pictures;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rate}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'buyer_id', 'score', 'is_anonymous', 'status', 'is_hidden', 'sort_order', 'is_auto',], 'integer'],
            [['member_id', 'buyer_id', 'score', 'is_anonymous', 'status', 'is_hidden', 'sort_order', 'is_auto',], 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'safe'],
            [['order_id', 'product_id', 'nickname'], 'string', 'max' => 64],
            [['product_title'], 'string', 'max' => 128],
            [['avatar_url', 'main_image', 'main_image_thumb'], 'string', 'max' => 256],
            [['content'], 'string', 'max' => 1000],
            [['ip'], 'string', 'max' => 18],
            [['order_sku_id'], 'unique'],

            [['rate_pictures', 'order_sku_id'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],

            'id' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'id'
                ],
                'value' => uniqid() . sprintf("%02d", rand(0, 99)) . '-r',
            ],

            [
                'class' => UploadBehavior::className(),
                'attribute' => 'rate_pictures',
                'pathAttribute' => 'path',
                'baseUrlAttribute' => 'base_url',
                'multiple' => true,
                'uploadRelation' => 'pictures', // relation name,

                'typeAttribute' => 'type',
                'sizeAttribute' => 'size', // 如果db中有size字段, 可设置一下, 用于保存文件的size
                'nameAttribute' => 'name',
                'orderAttribute' => 'order',
            ]
        ];
    }

    /**
     * 评价与订单中的sku关联, 每个订单中的sku都可以有不同的评价
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单ID',
            'member_id' => '商户ID',
            'buyer_id' => '买家ID',
            'product_id' => '商品ID',
            'product_title' => '商品标题',
            'shortTitle' => '商品标题',
            'content' => '评价内容',
            'shortContent' => '评价内容',
            'score' => '评分',
            'ip' => 'IP',
            'is_anonymous' => '匿名',
            'is_hidden' => '隐藏', // 不显示
            'nickname' => '买家昵称',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'rate_pictures' => '图片',
            'picturesCount' => '图片数',
            'sort_order' => '排序',
            'order_sku_id' => '订单SKU',
            'avatar_url' => '评价者头像',
            'is_auto' => '是自动好评',
            'main_image' => '商品图',
            'main_image_thumb' => '缩略图',
            'is_star' => '评价者是星钻会员',
        ];
    }

    /**
     * @inheritdoc
     * @return RateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RateQuery(get_called_class());
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

    public function getPictures()
    {
        return $this->hasMany(Picture::className(), ['global_sid' => 'id']);
    }

    public function getPicturesCount($condition = [])
    {
        return $this->getPictures()->where($condition)->count();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            // 要么有 order_sku_id, 要么有product_id + buyer_id
            if (!empty($this->order_sku_id)) {
                $this->order_id = ArrayHelper::getValue($this, 'orderSku.order_id');
                $this->product_id = ArrayHelper::getValue($this, 'orderSku.product_id');
                $this->member_id = ArrayHelper::getValue($this, 'orderSku.member_id');
                $this->buyer_id = ArrayHelper::getValue($this, 'orderSku.buyer_id');
                $this->nickname = ArrayHelper::getValue($this, 'orderSku.buyer.nickname');
                $this->product_title = ArrayHelper::getValue($this, 'orderSku.product.title');
                $this->avatar_url = ArrayHelper::getValue($this, 'orderSku.buyer.avatarImageUrl');
                $this->main_image = ArrayHelper::getValue($this, 'orderSku.main_image');
                $this->main_image_thumb = ArrayHelper::getValue($this, 'orderSku.main_image_thumb');
            } else {
                $this->member_id = ArrayHelper::getValue($this, 'product.member_id');
                $this->product_title = ArrayHelper::getValue($this, 'product.title');
                $this->nickname = ArrayHelper::getValue($this, 'buyer.nickname');
                $this->avatar_url = ArrayHelper::getValue($this, 'buyer.avatarImageUrl');
                $this->main_image = ArrayHelper::getValue($this, 'product.mainImageUrl');
                $this->main_image_thumb = ArrayHelper::getValue($this, 'product.mainThumbImageUrl');
            }
            $this->ip = empty($this->ip) ? Util::getIpAddr() : $this->ip;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            if ($this->orderSku) {
                $this->orderSku->is_rated = 1;
                $this->orderSku->rate_time = date('Y-m-d H:i:s');
                if (!$this->orderSku->save()) {
                    Yii::error([__METHOD__, __LINE__, $this->orderSku->errors]);
                    throw new HttpException(400, '操作失败');
                }
            }
            if ($this->product) {
                $this->product->updateCounters(['total_rate_score' => $this->score]);
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if ($this->product) {
            $this->product->updateCounters(['total_rate_score' => -$this->score]);
        }
        foreach ($this->pictures as $model) {
            $model->delete();
        }
    }

    public function getShortTitle()
    {
        return StringHelper::truncate($this->product_title, 20);
    }

    public function getShortContent()
    {
        return StringHelper::truncate($this->content, 20);
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
        $fields['order_sku_option_value_names'] = function ($model) {
            return ArrayHelper::getValue($model, 'orderSku.option_value_names');
        };
        $fields['order_sku_quantity'] = function ($model) {
            return ArrayHelper::getValue($model, 'orderSku.quantity');
        };
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'pictures';
        $fields[] = 'buyer';
        $fields[] = 'product';
        return $fields;
    }

}
