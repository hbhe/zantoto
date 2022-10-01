<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use mohorev\file\UploadImageBehavior;
use paulzi\materializedPath\MaterializedPathBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $is_leaf
 * @property string $name
 * @property string $keyword
 * @property string $description
 * @property string $unit
 * @property string $icon
 * @property string $path
 * @property integer $depth
 * @property integer $sort_order
 * @property integer $is_visual
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Category extends \common\models\ActiveRecord
{
    const ROOT_ID = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'depth', 'sort_order', 'is_visual', 'status', 'is_leaf'], 'integer'],
            [['parent_id', 'depth', 'sort_order', 'is_visual', 'status', 'is_leaf'], 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['keyword'], 'string', 'max' => 64],
            [['description', 'path'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 8],
            [['unit'], 'default', 'value' => '件'],
            [['name'], 'required'],

            [['icon'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],

//            [
//                'class' => AdjacencyListBehavior::className(),
//                'parentAttribute' => 'parent_id',
//                'sortable' => false,
//            ],
            [
                'class' => MaterializedPathBehavior::className(),
                'sortable' => false,
            ],

            'icon' => [
                'class' => UploadImageBehavior::class,
                'generateNewName' => function ($file) {
                    return md5(uniqid() . $file->name . rand(1, 1000)) . '.' . $file->extension;
                }, // form中多文件上传时用这个保证唯一性, 插件中的默然函数不行
                'instanceByName' => true, // 因为REST中想直接使用pay_filename=xxx.jpg, 不想用Trade[pay_filename]=xxx.jpg
                'attribute' => 'icon',
                //'scenarios' => ['insert', 'update'],
                'scenarios' => [self::SCENARIO_DEFAULT],
                //'placeholder' => '@storage/web/images/avatar.png',
                'path' => '@storage/web/images/category', // 定义文件存放的目录
                'url' => '@storageUrl/images/category',
                //'url' => '@backendUrl/img/share/{id}',
                'thumbs' => [ // 定义各种size的thumbs
                    'thumb' => ['width' => 100, 'height' => 100, 'quality' => 90],
                    //'preview' => ['width' => 200, 'height' => 200], // 'bg_color' => '000'
                ],
            ],
        ];
    }

    /**
     * 叶子结点才可以挑选规格
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => '父分类',
            'name' => '名称',
            'keyword' => '关键词',
            'description' => '描述',
            'is_leaf' => '叶子结点',
            'unit' => '单位',
            'icon' => '图标',
            'iconUrl' => '图标',
            'iconThumbUrl' => '图标',
            'path' => '路径',
            'depth' => '层级',
            'sort_order' => '排序',
            'is_visual' => '显示',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'optionsAliasString' => '关联规格',
        ];
    }

    static public function getRoot()
    {
        return self::findOne(self::ROOT_ID);
    }

    public static function getFirstCategoryOption()
    {
        $key = [__METHOD__];
        $data = Yii::$app->cache->get($key);
        if ($data !== false) {
            return $data;
        }

        $models = self::getRoot()->getChildren()->andWhere(['is_visual' => 1])->all();
        $value = ArrayHelper::map($models, 'id', 'name');
        Yii::$app->cache->set($key, $value, YII_ENV_DEV ? 6 : 6);
        return $value;
    }

    public function getParentsNode($includeSelf = true, $includeRoot = true)
    {
        $models = $this->getParents()->all();
        if ($includeSelf) {
            $models[] = $this;
        }
        if (!$includeRoot) {
            $root = array_shift($models);
        }
        return $models;
    }

    public function getParentsNodePath($glue = '-', $includeSelf = true, $includeRoot = false)
    {
        $models = $this->getParentsNode($includeSelf, $includeRoot);
        $names = ArrayHelper::getColumn($models, 'name');
        return implode($glue, $names);
    }

    public function getIconUrl()
    {
        return $this->getUploadUrl('icon');
    }

    public function getIconThumbUrl()
    {
        return $this->getThumbUploadUrl('icon', 'thumb');
    }

    public function getOptions()
    {
        return $this->hasMany(Option::className(), ['id' => 'option_id'])->viaTable(CategoryOption::tableName(), ['category_id' => 'id']);
    }

    public function getOptionsAliasString($glue = ',')
    {
        $arr = [];
        foreach ($this->options as $option) {
            $arr[] = $option->aliasString;
        }
        return implode($glue, $arr);
    }

    /**
     * option表中还有哪些可被关联到此category上
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getRestOptions()
    {
        $option_ids = $this->getOptions()->select(['id'])->column();
        $models = Option::find()->where(['not in', 'id', $option_ids])->all();
        return $models;
    }

    public function getRestOptionsDropDown()
    {
        $models = $this->getRestOptions();
        $arr = ArrayHelper::map($models, 'id', 'name');
        return $arr;
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['iconUrl'] = 'iconUrl';
        unset($fields['icon']);
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        return $fields;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $models = $this->getParents()->all();
            foreach ($models as $model) {
                $model->updateAttributes([
                    'is_leaf' => $model->isLeaf(),
                ]);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        $models = $this->getParents()->all();
        foreach ($models as $model) {
            $model->updateAttributes([
                'is_leaf' => $model->isLeaf(),
            ]);
        }
        parent::afterDelete();
    }


}
