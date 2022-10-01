<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property int $id
 * @property string $name Tag
 * @property int $frequency frequency
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tag',
            'frequency' => 'frequency',
        ];
    }

    static public function findAllByName($name)
    {
        return self::find()->where(['like', 'name', $name])->all();
    }
}
