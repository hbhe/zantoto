<?php
/**
 *  @link http://github.com/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @author 57620133@qq.com
 */

namespace common\models;

use common\wosotech\helper\Util;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\web\HttpException;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property string $id
 * @property integer $member_id
 * @property integer $category_id
 * @property string $category_path
 * @property integer $category_id1
 * @property integer $category_id2
 * @property integer $category_id3
 * @property string $spu_id
 * @property string $title
 * @property string $sub_title
 * @property string $brand_id
 * @property integer $is_platform
 * @property string $custom_brand
 * @property string $main_image_id
 * @property string $main_image
 * @property string $main_image_thumb
 * @property integer $quantity
 * @property string $price
 * @property string $cost_price
 * @property string $market_price
 * @property integer $sold_volume
 * @property string $award_revenue
 * @property string $award_fish
 * @property integer $award_coupon
 * @property integer $award_coupon_limit
 * @property integer $award_coupon_rest
 * @property integer $accept_coupon_ratio
 * @property integer $accept_coupon_cat
 * @property integer $accept_coupon_amount
 * @property integer $is_star_product
 * @property integer $shipping
 * @property integer $has_option
 * @property integer $sort_order
 * @property integer $status_listing
 * @property string $detail
 * @property string $memo
 * @property integer $total_rate_score
 * @property integer $status
 * @property integer $related_post_count
 * @property string $created_at
 * @property string $updated_at
 * @property string $listing_time
 * @property string $delisting_time
 */
class Product extends \common\models\ActiveRecord
{
    public $has_option_changed_flag;

    public $product_pictures;

    public $detail_pictures;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'category_id', 'category_id1', 'category_id2', 'category_id3', 'quantity', 'sold_volume', 'shipping', 'has_option', 'sort_order', 'status_listing', 'total_rate_score', 'status', 'is_platform', ], 'integer'],
            [['member_id', 'category_id', 'category_id1', 'category_id2', 'category_id3', 'quantity', 'sold_volume', 'shipping', 'sort_order', 'status_listing', 'total_rate_score', 'status', 'is_platform', ], 'default', 'value' => 0],
            [['price', 'cost_price', 'market_price', ], 'number'],
            [['price', 'cost_price', 'market_price', ], 'default', 'value' => 0],
            [['detail', 'brand_id',], 'string'],
            [['created_at', 'updated_at', 'listing_time', 'delisting_time'], 'safe'],
            [['id', 'spu_id', 'brand_id'], 'string', 'max' => 64], //'custom_brand'
            [['sub_title', 'category_path',], 'string', 'max' => 255], // 'main_image', 'main_image_thumb'
            [['title'], 'string', 'max' => 128],
            [['memo'], 'string', 'max' => 500],
            [['spu_id'], 'unique'],
            [['id'], 'unique'],

            [['has_option'], 'default', 'value' => 1],
            [['member_id', 'title'], 'required'],
            [['product_pictures', 'detail_pictures', 'main_image_id'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'id'
                ],
                'value' => uniqid(),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'member_id'
                ],
                'value' => Member::ROOT_ID,
                'preserveNonEmptyValues' => true,
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'spu_id'
                ],
                'value' => uniqid(),
                'preserveNonEmptyValues' => true,
            ],
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'product_pictures',
                'pathAttribute' => 'path',
                'baseUrlAttribute' => 'base_url',
                'multiple' => true,
                'uploadRelation' => 'productPictures', // relation name,
                'typeAttribute' => 'type',
                'sizeAttribute' => 'size', // 如果db中有size字段, 可设置一下, 用于保存文件的size
                'nameAttribute' => 'name',
                'orderAttribute' => 'order',
            ],
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'detail_pictures',
                'pathAttribute' => 'path',
                'baseUrlAttribute' => 'base_url',
                'multiple' => true,
                'uploadRelation' => 'detailPictures',
                'typeAttribute' => 'type',
                'sizeAttribute' => 'size',
                'nameAttribute' => 'name',
                'orderAttribute' => 'order',
            ],

        ];
    }

    public function transactions()
    {
        return [
            Model::SCENARIO_DEFAULT => self::OP_ALL
        ];
    }

    /**
     * 为了保证商城的灵活性与数据一致性，在设计时遵循了以下原则:  删除和修改公共的分类和规格库不影响已创建好的商品,
     * 删除和修改商品不影响已创建好的订单，也就是说即使商家删除了商品，用户的订单也应能完整地显示出来。因此在设计时,
     * 商品的规格采用了deep copy公共库中的规格而不是引用, 订单中的sku也是deep copy商品中的SKU
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '商品ID',
            'member_id' => '商户ID',
            'category_id' => '分类ID',
            'category_path' => '分类路径', // 如1/3/4, 这个也可被用来做商品SEARCH
            'categoryPathName' => '分类', // 带路径的中文分类
            'spu_id' => '商品货号',
            'title' => '标题',
            'shortTitle' => '标题',
            'sub_title' => '描述', // 子标题
            'brand_id' => '品牌',
            'main_image_id' => '主图ID',
            'mainThumbImageUrl' => '主图',
            'mainImageUrl' => '主图',
            'quantity' => '库存',
            'price' => '基础单价',
            'cost_price' => '成本价',
            'market_price' => '市场参考价',
            'sold_volume' => '销量',
            'shipping' => '需发货',
            'has_option' => '有规格',
            'sort_order' => '排序',
            'status_listing' => '状态', // 上架状态
            'detail' => '详情',
            'memo' => '备注',
            'total_rate_score' => '累计评分', // 评价分累计数
            'status' => '状态', // 暂时未用
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'listing_time' => '上架时间',
            'delisting_time' => '下架时间',
            'statusListingString' => '状态',
            'category_id1' => '一级分类', // 商品编辑时, 要输入category_id1, category_id2, category_id3, category_id, 最终要生成category_id, 这3个id保存在db中是为了方便做搜索(其实也可以用category_path来做)
            'category_id2' => '二级分类',
            'category_id3' => '三级分类',
            'product_pictures' => '商品图',
            'detail_pictures' => '详情图',
            'is_platform' => '是平台商品',
        ];
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->clearOptionAndSku();
        Wishlist::deleteAll(['product_id' => $this->id]);
        foreach ($this->productPictures as $model) {
            $model->delete();
        }
        foreach ($this->detailPictures as $model) {
            $model->delete();
        }
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->is_platform = $this->member_id == Member::ROOT_ID ? 1 : 0;
        }
        $this->category_id = $this->category_id3 ?: $this->category_id2 ?: $this->category_id1 ?: 0;
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        // 先触发event, 保存好图片后处理
        parent::afterSave($insert, $changedAttributes);

        if ($insert || ArrayHelper::getValue($changedAttributes, 'status_listing') != $this->status_listing) {
            if ($this->status_listing == self::STATUS_LISTING_ON) {
                $this->updateAttributes(['listing_time' => date('Y-m-d H:i:s')]);
            }
            if ($this->status_listing == self::STATUS_LISTING_OFF) {
                $this->delisting_time = date('Y-m-d H:i:s');
                $this->updateAttributes(['delisting_time' => date('Y-m-d H:i:s')]);
            }
        }

        // can not build image url without @webroot
        $mainImage = $this->getProductPictures()->andWhere(['like', 'type', 'image'])->orderBy(['created_at' => SORT_ASC])->one();
        if ($mainImage != null) {
            if ($mainImage->global_sid != $this->main_image_id) {
                $this->updateAttributes([
                    'main_image_id' => $mainImage->global_sid,
                ]);
            }
        }

        // 商品的option, category的修改会导致OPTION和SKU的重新初始化
        $this->has_option_changed_flag = false;

        // 如果是新增, 或者分类发生改变, 需初始化规格库
        if ($insert || ArrayHelper::getValue($changedAttributes, 'category_id') != $this->category_id) {
            $this->updateAttributes([
                //'category_id' => $this->category_id3 ?: $this->category_id2 ?: $this->category_id1 ?: 0,
                'category_id' => $this->category_id,
                'category_path' => ArrayHelper::getValue($this, 'category.path'),
            ]);

            $this->has_option_changed_flag = true; // 或者可改成由人工在界上面去判断执行? TODO
        }

        // 商品新增时, 如果有规格则需要初始化规格, 无规格商品是否要自动加一条SKU? 就放在配置SKU时去检查吧 TODO
        if ($insert && $this->has_option) {
            $this->has_option_changed_flag = true;
        }

        // 商品修改时, 如果改变了是否有规格, 也需要初始化规格
        if (ArrayHelper::getValue($changedAttributes, 'has_option') != $this->has_option) {
            $this->has_option_changed_flag = true;
        }

        // 规格的个数和值的个数发生变化, 也要考虑重新生成OPTION&SKU, 由人工在界上面去判断执行? TODO

        if ($this->has_option_changed_flag) {
            // 不能在这删, 删了白删, 输入界面还是有原来的OPTION输入; 在controller中这些数据会被利用进行创建, 所以只能将删除移到controller中执行
            // $this->initCategoryOption();
        }

        // 如果下架或删除, 要把购物车和收藏夹中的商品删除掉, TODO
    }

    /**
     * @inheritdoc
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    public function getProductOptions()
    {
        return $this->hasMany(ProductOption::className(), ['product_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getSkus()
    {
        return $this->hasMany(Sku::className(), ['product_id' => 'id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getCategory1()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id1']);
    }

    public function getCategory2()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id2']);
    }

    public function getCategory3()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id3']);
    }

    public function getCategoryPathName()
    {
        return $this->category ? $this->category->getParentsNodePath() : '';
    }

    public function getStatusListingString()
    {
        return ArrayHelper::getValue(self::getStatusListingArray(), $this->status_listing);
    }

    const STATUS_LISTING_WAIT = 0;
    const STATUS_LISTING_ON = 1;
    const STATUS_LISTING_OFF = 2;
    const STATUS_LISTING_TRASH = 3;

    public static function getStatusListingArray()
    {
        return [
            self::STATUS_LISTING_WAIT => '待审核',
            self::STATUS_LISTING_ON => '已上架',
            self::STATUS_LISTING_OFF => '已下架',
        ];
    }

    const ACCEPT_COUPON_CAT_RATIO = 0;
    const ACCEPT_COUPON_CAT_NUMBER = 1;

    public static function getAcceptCouponCatArray()
    {
        return [
            self::ACCEPT_COUPON_CAT_RATIO => '按比例',
            self::ACCEPT_COUPON_CAT_NUMBER => '按金额',
        ];
    }

    public function getAcceptCouponCatString()
    {
        return ArrayHelper::getValue(self::getAcceptCouponCatArray(), $this->accept_coupon_cat);
    }

    public function getAcceptCouponCatComboString()
    {
        return $this->acceptCouponCatString . ': ' . ($this->accept_coupon_cat == self::ACCEPT_COUPON_CAT_RATIO ? $this->accept_coupon_ratio : $this->accept_coupon_amount);
    }

    public function getShortTitle()
    {
        return StringHelper::truncate($this->title, 10);
    }

    public function getMainImage()
    {
        return $this->hasOne(ProductPicture::className(), ['global_sid' => 'id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getMainThumbImageUrl()
    {
        return ArrayHelper::getValue($this, 'mainImage.thumbImageUrl');
    }

    public function getBigMainThumbImageUrl()
    {
        return ArrayHelper::getValue($this, 'mainImage.thumbImageUrl');
    }

    public function getMainImageUrl()
    {
        return ArrayHelper::getValue($this, 'mainImage.imageUrl');
    }

    public function getProductPictures()
    {
        return $this->hasMany(ProductPicture::className(), ['global_sid' => 'id']);
    }

    public function getProductPicturesCount($condition = [])
    {
        return $this->getProductPictures()->where($condition)->count();
    }

    public function getDetailPictures()
    {
        return $this->hasMany(ProductDetailPicture::className(), ['global_sid' => 'id']);
    }

    public function getDetailPicturesCount($condition = [])
    {
        return $this->getDetailPictures()->where($condition)->count();
    }

    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['product_id' => 'id']);
    }

    /**
     * 汇总sku库存作为商品的库存
     */
    public function flushQuantity()
    {
        $quantity = Sku::find()->where(['product_id' => $this->id])->sum('quantity');
        $this->updateAttributes(['quantity' => empty($quantity) ? 0 : $quantity]);
    }

    /**
     * 重建商品OPTION
     */
    public function initCategoryOption()
    {
        $this->clearOptionAndSku();

        // 对于无规格商品无须重建OPTION
        if (!$this->has_option) {
            // 提前加一条SKU
            $this->initSku();
            return;
        }

        // 如果不是平台, 不用导入分类中的OPTION
        if (!$this->is_platform) {
            return;
        }

        // 根据分类重建OPTION
        $category = $this->category;
        if (!$category) {
            Yii::error([__METHOD__, __LINE__, 'this product has not category!', $this->id]);
            return;
        }
        foreach ($category->options as $option) {
            $productOption = new ProductOption();
            $productOption->setAttributes([
                'product_id' => $this->id,
                'name' => $option->name,
            ]);
            if ($productOption->save()) {
                foreach ($option->values as $value) {
                    $productOptionValue = new ProductOptionValue();
                    $productOptionValue->setAttributes([
                        'product_option_id' => $productOption->id,
                        'name' => $value->name,
                    ]);
                    if (!$productOptionValue->save()) {
                        Yii::error([__METHOD__, __LINE__, $productOptionValue->errors]);
                        throw new HttpException(500, implode(',', $productOptionValue->errorSummary));
                    }
                }
            } else {
                Yii::error([__METHOD__, __LINE__, $productOption->errors]);
                throw new HttpException(500, implode(',', $productOption->errorSummary));
            }
        }
    }

    /**
     * 重建SKU
     */
    public function initSku()
    {
        $this->clearSku();

        // 对于无规格商品, 也生成一个SKU
        if (!$this->has_option) {
            $model = new Sku();
            $model->setAttributes([
                'member_id' => $this->member_id,
                'product_id' => $this->id,
                'sku_code' => $this->id,
                'query_string' => '',
                'option_value_ids' => Json::encode([]),
                'option_value_names' => Json::encode([]),
                'price' => $this->price,
                'quantity' => $this->quantity,
            ]);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                throw new HttpException(500, implode(',', $model->errorSummary));
            }
            return;
        }

        // 根据OPTION, 重建SKU
        $arr1 = [];
        $arr2 = [];
        foreach ($this->productOptions as $productOption) {
            $values = $productOption->productOptionValues;
            $arr1[$productOption->id] = ArrayHelper::getColumn($values, 'id');
            $arr2[$productOption->name] = ArrayHelper::getColumn($values, 'name');
        }

        $rows1 = Util::cartesian($arr1);
        $rows2 = Util::cartesian($arr2);
        \Yii::info(['cartesian', $this->id, $arr1, $arr2, $rows1, $rows2]);
        /*
                // rows1
                [
                    [
                        37 => 34,
                        38 => 36,
                    ],
                    [
                        37 => 35,
                        38 => 36,
                    ],
                ]
                // row2s
                [
                    [
                        'A' => 'A1',
                        'B' => 'B1',
                    ],
                    [
                        'A' => 'A2',
                        'B' => 'B1',
                    ],
                ]
        */
        $limit = count($rows1);
        for ($i = 0; $i < $limit; $i++) {
            $model = new Sku();
            $price = $this->price;
            foreach ($rows1[$i] as $product_option_id => $product_option_value_id) {
                $productOptionValue = ProductOptionValue::findOne($product_option_value_id);
                $price += ArrayHelper::getValue($productOptionValue, 'price');
            }
            $model->setAttributes([
                'member_id' => $this->member_id,
                'product_id' => $this->id,
                'query_string' => Product::getSkuQueryString($rows1[$i]),
                'option_value_ids' => Json::encode($rows1[$i]),
                'option_value_names' => Json::encode($rows2[$i]),
                'price' => $price,
            ]);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                throw new HttpException(500, implode(',', $model->errorSummary));
            }
        }
    }

    /**
     * 清空此商品属性和SKU
     */
    public function clearOptionAndSku()
    {
        foreach ($this->productOptions as $model) {
            $n = $model->delete();
        }
        $this->clearSku();
    }

    public function clearSku()
    {
        foreach ($this->skus as $model) {
            $model->delete();
        }
    }

    /*
        Yii::info(Product::getSkuQueryString([4 => 4, 3 => 1]));
        Yii::info(Product::getSkuQueryString([3 => 1, 4 => 4]));
    */
    static public function getSkuQueryString($arr)
    {
        $tmp = $arr;
        ksort($tmp);
        $arr = [];
        foreach ($tmp as $key => $value) {
            $arr[] = "{$key}:{$value}";
        }
        return implode(',', $arr);
    }

    public function getOptionsTableHtml()
    {
        if (empty($this->productOptions)) {
            return '';
        }
        $text = '<hr/>';
        $text .= "<h3 align=\"center\"></h3>";

        $text .= '<table class="table table-bordered detail-view"><thead>';
        $text .= '<tr>';
        $text .= '<th width="150">规格</th>';
        $text .= '<th width="150">可取值</th>';
        $text .= '<th width="150">价格增加</th>';
        //$text .= '<th width="150">排序</th>';
        $text .= '</tr>';
        $text .= '</thead><tbody>';

        foreach ($this->productOptions as $option) {
            foreach ($option->productOptionValues as $value) {
                $line = '';
                $line .= self::getCat1Td($option);
                $line .= "<td>{$value->name}</td>";
                $line .= "<td>{$value->price}</td>";
                //$line .= "<td>{$value->sort_order}</td>";
                $text .= "<tr>$line</tr>";
            }
        }
        $text .= '</tbody>';
        $text .= '</table>';

        return $text;
    }

    static public function getCat1Td($option)
    {
        static $tags = [];
        if (!empty($tags[$option->id])) {
            return '';
        }
        $tags[$option->id] = true;
        $count = count($option->productOptionValues);
        return "<td rowspan=\"$count\">{$option->name}</td>";
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['detail']);
        unset($fields['main_image_id']);
        unset($fields['shipping']);
        unset($fields['status']);
        $fields['categoryPathName'] = 'categoryPathName';
        $fields['mainImageUrl'] = 'mainImageUrl';
        $fields['mainThumbImageUrl'] = 'mainThumbImageUrl';

        if ($this->scenario == static::SCENARIO_VIEW) {
            $fields['isInMyWishlist'] = function ($model) {
                if (Yii::$app->user->isGuest) {
                    return 0;
                }
                return Wishlist::findOne(['member_id' => Yii::$app->user->id, 'product_id' => $this->id]) === null ? 0 : 1;
            };
        }

        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'productPictures';
        $fields[] = 'detailPictures';
        $fields[] = 'mainImage';
        $fields[] = 'skus';

        return $fields;
    }

}

