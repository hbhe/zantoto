<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%shop_category}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property integer $sort_order
 * @property string $created_at
 * @property string $updated_at
 */
class ShopCategory extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort_order'], 'integer'],
            [['parent_id', 'sort_order'], 'default', 'value' => 0],
            [['parent_id', 'sort_order'], 'filter', 'filter' => 'intval'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['name'], 'required'],
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
            'parent_id' => '父分类',
            'name' => '名称',
            'sort_order' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function beforeDelete()
    {
        if ($this->getChildren()->count()) {
            return false;
        }
        return parent::beforeDelete();
    }

    public function getParent()
    {
        return $this->hasOne(ShopCategory::className(), ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(ShopCategory::className(), ['parent_id' => 'id']);
    }

}
