<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%product_option_value}}".
 *
 * @property integer $id
 * @property integer $product_option_id
 * @property integer $member_id
 * @property string $product_id
 * @property integer $option_id
 * @property string $option_name
 * @property string $name
 * @property string $option_value_id
 * @property string $option_value_name
 * @property integer $sort_order
 * @property integer $price
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class ProductOptionValue extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_option_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_option_id', 'sort_order', 'status'], 'integer'], //'member_id', 'option_id',
            [['product_option_id', 'sort_order', 'status', 'price'], 'default', 'value' => 0], // 'member_id', 'option_id',
            [['status', 'sort_order'], 'filter', 'filter' => 'intval'],
            [['created_at', 'updated_at'], 'safe'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 128],
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
            'product_option_id' => '规格ID',
            'sort_order' => '排序',
            'price' => '价格调整', // 0.5表示在原价的基础上加0.5, -0.5表示少0.5
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
