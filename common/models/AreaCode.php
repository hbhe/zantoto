<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use paulzi\adjacencyList\AdjacencyListBehavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%area_code}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property integer $parent_id
 * @property string $zip
 */
class AreaCode extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%area_code}}';
    }

    /**
     * @inheritdoc
     * @return AreaCodeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AreaCodeQuery(get_called_class());
    }

    public static function getChina()
    {
        return AreaCode::findOne(['id' => '1']);
    }

    public static function getChinaProvinceAreaCode()
    {
        return $models = self::getChina()->children;
        // return AreaCode::find()->where(['parent_id' => 1])->all();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['zip', 'id', 'parent_id'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '层级',
            'name' => '名称',
            'parent_id' => '上级代码',
            'zip' => '邮编',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => AdjacencyListBehavior::className(),
                'parentAttribute' => 'parent_id',
                'sortable' => false,
            ],
        ];
    }

    public function getDescendantsIdsX($andSelf = true)
    {
        $key = [__METHOD__, $andSelf, $this->id];
        $data = Yii::$app->cache->get($key);
        if ($data !== false) {
            return $data;
        }

        $descendantsIds = $this->getDescendantsIds(null, true);
        if ($andSelf) {
            $descendantsIds[] = $this->id;
        }

        Yii::$app->cache->set($key, $descendantsIds, 24 * 3600);
        return $descendantsIds;
    }

    public function getPathName($glue = '-')
    {
        $key = [__METHOD__, $glue, $this->id];
        $data = Yii::$app->cache->get($key);
        if ($data !== false) {
            return $data;
        }

        $names = [];
        foreach ($this->parents as $parent) {
            $names[] = $parent->name;
        }
        $names[] = $this->name;
        // 去掉最外面的中国
        array_shift($names);

        Yii::$app->cache->set($key, implode($glue, $names), 24 * 3600);
        return implode($glue, $names);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['pathName'] = function ($model) {
            return $model->getPathName('');
        };
        return $fields;
    }

    public static function getProvinceOption()
    {
        $key = [__METHOD__];
        $data = Yii::$app->cache->get($key);
        if ($data !== false) {
            return $data;
        }

        $models = self::getChinaProvinceAreaCode();
        $value = ArrayHelper::map($models, 'id', 'name');
        Yii::$app->cache->set($key, $value, 24 * 3600);
        return $value;
    }

    public function getMychildren()
    {
        $key = [__METHOD__, $this->id];
        $data = Yii::$app->cache->get($key);
        if ($data !== false) {
            return $data;
        }
        $sons = $this->children;
        Yii::$app->cache->set($key, $sons, YII_DEBUG ? 1 : 24 * 3600);
        return $sons;
    }

    public static function getChildrenOption($area_parent_id)
    {
        $model = self::findOne(['id' => $area_parent_id]);
        return ArrayHelper::map($model->getMychildren(), 'id', 'name');
    }

}
