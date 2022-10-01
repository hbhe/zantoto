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
 * This is the model class for table "{{%product_option}}".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $product_id
 * @property string $name
 * @property integer $required
 * @property string $default_value
 * @property integer $status
 * @property integer $sort_order
 * @property string $created_at
 * @property string $updated_at
 */
class ProductOption extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'required', 'status', 'sort_order'], 'integer'],
            [['member_id', 'required', 'status', 'sort_order'], 'default', 'value' => 0],
            [['status', 'sort_order'], 'filter', 'filter' => 'intval'],
            [['created_at', 'updated_at'], 'safe'],
            [['product_id', 'default_value', 'name'], 'string', 'max' => 64],
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

    public function afterDelete()
    {
        foreach ($this->productOptionValues as $model) {
            $model->delete();
        }
        parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '商户ID',
            'product_id' => '商品ID',
            'name' => '规格',
            'required' => '必输项',
            'default_value' => '默认值',
            'status' => '状态',
            'sort_order' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'productOptionValuesString' => '可取值',
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->member_id = ArrayHelper::getValue($this, 'product.member_id');
        }
        return parent::beforeSave($insert);
    }

    public function getProductOptionValues()
    {
        return $this->hasMany(ProductOptionValue::className(), ['product_option_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getProductOptionValuesString($glue = '|')
    {
        $arr = $this->getProductOptionValues()->orderBy(['sort_order' => SORT_ASC])->select(['name'])->column();
        return implode($glue, $arr);
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'productOptionValues';
        return $fields;
    }

}
