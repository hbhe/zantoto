<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%option}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $alias
 * @property integer $sort_order
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Option extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'sort_order', 'status'], 'integer'],
            [['type', 'sort_order', 'status'], 'default', 'value' => 0],
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'alias'], 'string', 'max' => 64],
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

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'name' => '属性名称', // 前台显示
            'alias' => '别名', // 后台显示用
            'status' => '状态',
            'sort_order' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'valuesString' => '可取值',
        ];
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public function getValues()
    {
        return $this->hasMany(OptionValue::className(), ['option_id' => 'id']);
    }

    public function getValuesString($glue = '|')
    {
        $arr = $this->getValues()->orderBy(['sort_order' => SORT_ASC])->select(['name'])->column();
        return implode($glue, $arr);
    }

    public function getAliasString()
    {
        return "{$this->name}:{$this->valuesString}";
    }

    public function beforeDelete()
    {
        foreach ($this->values as $model) {
            $model->delete();
        }
        return parent::beforeDelete();
    }


}
