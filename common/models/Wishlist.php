<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%wishlist}}".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $product_id
 * @property string $created_at
 * @property string $updated_at
 */
class Wishlist extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wishlist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['product_id'], 'string', 'max' => 64],
            [['member_id', 'product_id'], 'unique', 'targetAttribute' => ['member_id', 'product_id']],

            [['product_id'], 'exist', 'targetRelation' => 'product'],
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

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '会员ID',
            'product_id' => '商品ID',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'member';
        $fields[] = 'product';
        return $fields;
    }

}
