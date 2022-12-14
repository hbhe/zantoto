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
            'sku_code' => 'SKU??????',
            'member_id' => '??????ID',
            'product_id' => '??????ID',
            'option_value_ids' => '??????ID', // {"1":2,"3":5}  ??????"1"?????????product_option?????????id, 2:?????????product_option_value??????id
            'option_value_names' => '?????????', // {"??????":"??????","??????":"L"}
            'query_string' => '??????KEY',   // 1:2,3:5 ????????????????????????????????????(?????????sku_code????????????)
            'quantity' => '??????',
            'price' => '??????', // sku???????????? = ?????????????????? + n????????????????????????
            'sold_volume' => 'SKU??????',
            'image' => 'SKU??????', // ????????????, ???black, yellow???????????????
            'sort_order' => '??????',
            'status' => '??????',
            'created_at' => '????????????',
            'updated_at' => '????????????',
            'acceptCouponAmount' => '?????????????????????', // ???SKU
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
     * ????????????sku???????????????
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
     * sku??????????????????, ??????????????????????????????
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
