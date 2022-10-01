<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%option_value}}".
 *
 * @property integer $id
 * @property integer $option_id
 * @property string $name
 * @property string $image
 * @property integer $sort_order
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class OptionValue extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%option_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_id', 'sort_order', 'status'], 'integer'],
            [['option_id', 'sort_order', 'status'], 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'image'], 'string', 'max' => 255],

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
            'option_id' => 'Option ID',
            'name' => '属性值',
            'image' => '图片',
            'sort_order' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
