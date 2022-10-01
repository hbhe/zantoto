<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%brand}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $letter
 * @property integer $sort_order
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Brand extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 255],
            [['letter'], 'string', 'max' => 1],
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
            'name' => '名称',
            'description' => 'Description',
            'letter' => '首字母',
            'sort_order' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
