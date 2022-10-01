<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%sku}}".
 *
 * @property integer $id
 * @property string $sku_code
 * @property integer $member_id
 * @property string $product_id
 * @property string $option_value_ids
 * @property string $option_value_names
 * @property string $query_string
 * @property string $option_value_names_md5
 * @property integer $quantity
 * @property string $price
 * @property integer $sold_volume
 * @property string $image
 * @property integer $sort_order
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Product $product
 */
class Sku extends \common\models\ActiveRecord
{
    public $optionValueIds;
    public $optionValueNames;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sku}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'quantity', 'sold_volume', 'sort_order', 'status'], 'integer'],
            [['member_id', 'quantity', 'sold_volume', 'sort_order', 'status'], 'default', 'value' => 0],
            [['product_id'], 'required'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['sku_code', 'product_id'], 'string', 'max' => 64],
            [['query_string', 'option_value_ids', 'image'], 'string', 'max' => 255],
            [['option_value_names'], 'string', 'max' => 500],
            [['query_string'], 'unique'],
            [['sku_code'], 'unique'],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'sku_code'
                ],
                'preserveNonEmptyValues' => true,
                'value' => uniqid(),
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
            'sku_code' => 'SKU编码',
            'member_id' => '商户ID',
            'product_id' => '商品ID',
            'option_value_ids' => '规格ID', // {"1":2,"3":5}  其中"1"代表在product_option表中的id, 2:表示在product_option_value中的id
            'option_value_names' => '规格值', // {"颜色":"黄色","尺码":"L"}
            'query_string' => '查询KEY',   // 1:2,3:5 在前端查询时传入此字符串(代替用sku_code进行查询)
            'quantity' => '库存',
            'price' => '单价', // sku的初始值 = 商品基础单价 + n个属性的增量价格
            'sold_volume' => 'SKU销量',
            'image' => 'SKU图片', // 暂时未用, 如black, yellow等不同图片
            'sort_order' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'acceptCouponAmount' => '单件可收券金额', // 本SKU
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getCarts()
    {
        return $this->hasMany(Cart::className(), ['sku_code' => 'sku_code']);
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->optionValueIds = empty($this->option_value_ids) ? [] : Json::decode($this->option_value_ids);
        $this->optionValueNames = empty($this->option_value_names) ? [] : Json::decode($this->option_value_names);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach ($this->carts as $model) {
            $model->delete();
        }
        if ($this->product) {
            $this->product->flushQuantity();
        }
    }

    /**
     * 计算一下sku的初始价格
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (empty($this->price)) {
            $optionsPrice = 0;
            foreach ($this->optionValueIds as $optionId => $valueId) {
                $productOptionValue = ProductOptionValue::findOne(['id' => $valueId]);
                $optionsPrice += $productOptionValue->price;
            }
            $this->price = $this->product->price + $optionsPrice;
        }
        return parent::beforeSave($insert);
    }

    /**
     * sku的库存变化时, 重新计算商品的总库存
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            return;
        }

        if (isset($changedAttributes['quantity']) && $changedAttributes['quantity'] != $this->quantity) {
            $this->product->flushQuantity();
        }
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        return $fields;
    }

}
