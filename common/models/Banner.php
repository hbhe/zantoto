<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%banner}}".
 *
 * @property integer $id
 * @property integer $cat
 * @property string $title
 * @property string $detail
 * @property integer $img_id
 * @property string $img_url
 * @property integer $jump_type
 * @property string $url
 * @property integer $app_function_id
 * @property integer $second
 * @property integer $sort_order
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Banner extends \common\models\ActiveRecord
{
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%banner}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat', 'img_id', 'jump_type', 'app_function_id', 'second', 'sort_order', 'status'], 'integer'],
            [['cat', 'img_id', 'jump_type', 'app_function_id', 'second', 'sort_order', 'status'], 'default', 'value' => 0],
            [['detail'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'url'], 'string', 'max' => 255],
            [['img_url'], 'string', 'max' => 512],

            [['title', 'jump_type',], 'required'],
            [['url'], 'url'],
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
            'cat' => '类型',
            'title' => '标题',
            'detail' => '说明',
            'img_id' => '图片',
            'img_url' => '图片',
            'imageUrl' => '图片',
            'jump_type' => '跳转类型',
            'url' => 'URL',
            'app_function_id' => 'APP原生功能ID',
            'second' => '停留时间',
            'sort_order' => '排序',
            'status' => '有效',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'jumpTypeString' => '跳转类型',
            'appFunctionString' => 'APP原生功能',
        ];
    }

    const CAT_HOMEPAGE_SLIDE = 1;
    const CAT_HOMEPAGE_PRODUCT = 2;

    public static function getCatArray()
    {
        return [
            self::CAT_HOMEPAGE_SLIDE => '首页轮播',
            self::CAT_HOMEPAGE_PRODUCT => '商品导航',
        ];
    }

    public function getCatString()
    {
        return ArrayHelper::getValue(self::getCatArray(), $this->cat);
    }

    public static function getNavItems()
    {
        $items = [];
        $cats = self::getCatArray();
        foreach ($cats as $key => $val) {
            $items[] = [
                'label' => $val,
                'url' => ['/banner/index', 'cat' => $key],
                'active' => Yii::$app->request->get('cat') == $key,
            ];
        }
        return $items;
    }

    public function getImageUrl($width = 9999, $height = 9999)
    {
        return \Yii::$app->imagemanager->getImagePath($this->img_id, $width, $height); // get originate picture
    }

    public function getJumpTypeString()
    {
        return ArrayHelper::getValue(self::getJumpTypeArray(), $this->jump_type);
    }

    public static function getJumpTypeArray()
    {
        return [
            1 => 'URL',
            2 => '无链接',
            3 => 'APP原生功能',
        ];
    }

    public function getAppFunctionString()
    {
        return ArrayHelper::getValue(self::getAppFunctionArray(), $this->jump_type);
    }

    /**
     * 原生APP功能列表
     * @return array
     */
    public static function getAppFunctionArray()
    {
        return [
            1 => '注册邀请',
            2 => '所有商品',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['img_id']);
        unset($fields['img_url']);
        $fields['imageUrl'] = 'imageUrl';
        return $fields;
    }

}
