<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%category_option}}".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $option_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class CategoryOption extends \common\models\ActiveRecord
{
    public $options_ids;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category_option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'option_id', 'status'], 'integer'],
            [['category_id', 'option_id', 'status'], 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'safe'],

            [['options_ids'], 'safe'],
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
            'category_id' => '分类ID',
            'option_id' => '规格ID',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'options_id' => '规格',
        ];
    }

    public function getOption()
    {
        return $this->hasOne(Option::className(), ['id' => 'option_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

}
