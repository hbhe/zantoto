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
                'sizeAttribute' => 'size', // ??????db??????size??????, ???????????????, ?????????????????????size
                'nameAttribute' => 'name',
                'orderAttribute' => 'order',
            ]
        ];
    }

    /**
     * ?????????????????????sku??????, ??????????????????sku???????????????????????????
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '??????ID',
            'member_id' => '??????ID',
            'buyer_id' => '??????ID',
            'product_id' => '??????ID',
            'product_title' => '????????????',
            'shortTitle' => '????????????',
            'content' => '????????????',
            'shortContent' => '????????????',
            'score' => '??????',
            'ip' => 'IP',
            'is_anonymous' => '??????',
            'is_hidden' => '??????', // ?????????
            'nickname' => '????????????',
            'status' => '??????',
            'created_at' => '????????????',
            'updated_at' => '????????????',
            'rate_pictures' => '??????',
            'picturesCount' => '?????????',
            'sort_order' => '??????',
            'order_sku_id' => '??????SKU',
            'avatar_url' => '???????????????',
            'is_auto' => '???????????????',
            'main_image' => '?????????',
            'main_image_thumb' => '?????????',
            'is_star' => '????????????????????????',
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
            // ????????? order_sku_id, ?????????product_id + buyer_id
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
                    throw new HttpException(400, '????????????');
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
