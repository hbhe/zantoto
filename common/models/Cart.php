<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%cart}}".
 *
 * @property integer $id
 * @property integer $buyer_id
 * @property integer $sku_id
 * @property string $product_id
 * @property string $sku_code
 * @property integer $quantity
 * @property string $created_at
 * @property string $updated_at
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cart}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['buyer_id', 'quantity', 'sku_id'], 'integer'],
            [['buyer_id', 'quantity', 'sku_id'], 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'safe'],
            [['product_id', 'sku_code'], 'string', 'max' => 64],

            [['sku_id'], 'exist', 'targetRelation' => 'sku'],
            [['buyer_id'], 'exist', 'targetRelation' => 'buyer'],
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
            'buyer_id' => '买家ID',
            'product_id' => '商品ID',
            'sku_id' => 'SKU ID',
            'sku_code' => 'SKU编码',
            'quantity' => '数量',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getBuyer()
    {
        return $this->hasOne(Member::className(), ['id' => 'buyer_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getSku()
    {
        // return $this->hasOne(Sku::className(), ['sku_code' => 'sku_code']);
        return $this->hasOne(Sku::className(), ['id' => 'sku_id']);
    }

    public function beforeValidate()
    {
        if (empty($this->sku_id) && !empty($this->sku_code)) {
            $model = Sku::findOne(['sku_code' => $this->sku_code]);
            $this->sku_id = $model->id;
        }
        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if (empty($this->product_id)) {
            $this->product_id = ArrayHelper::getValue($this, 'sku.product_id');
        }
        return parent::beforeSave($insert);
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'product';
        $fields[] = 'sku';

        return $fields;
    }

}
