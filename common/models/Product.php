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
                'sizeAttribute' => 'size', // ??????db??????size??????, ???????????????, ?????????????????????size
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
     * ????????????????????????????????????????????????????????????????????????????????????:  ????????????????????????????????????????????????????????????????????????,
     * ??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????,
     * ????????????????????????deep copy????????????????????????????????????, ????????????sku??????deep copy????????????SKU
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '??????ID',
            'member_id' => '??????ID',
            'category_id' => '??????ID',
            'category_path' => '????????????', // ???1/3/4, ??????????????????????????????SEARCH
            'categoryPathName' => '??????', // ????????????????????????
            'spu_id' => '????????????',
            'title' => '??????',
            'shortTitle' => '??????',
            'sub_title' => '??????', // ?????????
            'brand_id' => '??????',
            'main_image_id' => '??????ID',
            'mainThumbImageUrl' => '??????',
            'mainImageUrl' => '??????',
            'quantity' => '??????',
            'price' => '????????????',
            'cost_price' => '?????????',
            'market_price' => '???????????????',
            'sold_volume' => '??????',
            'shipping' => '?????????',
            'has_option' => '?????????',
            'sort_order' => '??????',
            'status_listing' => '??????', // ????????????
            'detail' => '??????',
            'memo' => '??????',
            'total_rate_score' => '????????????', // ??????????????????
            'status' => '??????', // ????????????
            'created_at' => '????????????',
            'updated_at' => '????????????',
            'listing_time' => '????????????',
            'delisting_time' => '????????????',
            'statusListingString' => '??????',
            'category_id1' => '????????????', // ???????????????, ?????????category_id1, category_id2, category_id3, category_id, ???????????????category_id, ???3???id?????????db???????????????????????????(??????????????????category_path??????)
            'category_id2' => '????????????',
            'category_id3' => '????????????',
            'product_pictures' => '?????????',
            'detail_pictures' => '?????????',
            'is_platform' => '???????????????',
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
        // ?????????event, ????????????????????????
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

        // ?????????option, category??????????????????OPTION???SKU??????????????????
        $this->has_option_changed_flag = false;

        // ???????????????, ????????????????????????, ?????????????????????
        if ($insert || ArrayHelper::getValue($changedAttributes, 'category_id') != $this->category_id) {
            $this->updateAttributes([
                //'category_id' => $this->category_id3 ?: $this->category_id2 ?: $this->category_id1 ?: 0,
                'category_id' => $this->category_id,
                'category_path' => ArrayHelper::getValue($this, 'category.path'),
            ]);

            $this->has_option_changed_flag = true; // ???????????????????????????????????????????????????? TODO
        }

        // ???????????????, ???????????????????????????????????????, ???????????????????????????????????????SKU? ???????????????SKU??????????????? TODO
        if ($insert && $this->has_option) {
            $this->has_option_changed_flag = true;
        }

        // ???????????????, ??????????????????????????????, ????????????????????????
        if (ArrayHelper::getValue($changedAttributes, 'has_option') != $this->has_option) {
            $this->has_option_changed_flag = true;
        }

        // ??????????????????????????????????????????, ????????????????????????OPTION&SKU, ????????????????????????????????????? TODO

        if ($this->has_option_changed_flag) {
            // ???????????????, ????????????, ??????????????????????????????OPTION??????; ???controller???????????????????????????????????????, ???????????????????????????controller?????????
            // $this->initCategoryOption();
        }

        // ?????????????????????, ????????????????????????????????????????????????, TODO
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
            self::STATUS_LISTING_WAIT => '?????????',
            self::STATUS_LISTING_ON => '?????????',
            self::STATUS_LISTING_OFF => '?????????',
        ];
    }

    const ACCEPT_COUPON_CAT_RATIO = 0;
    const ACCEPT_COUPON_CAT_NUMBER = 1;

    public static function getAcceptCouponCatArray()
    {
        return [
            self::ACCEPT_COUPON_CAT_RATIO => '?????????',
            self::ACCEPT_COUPON_CAT_NUMBER => '?????????',
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
     * ??????sku???????????????????????????
     */
    public function flushQuantity()
    {
        $quantity = Sku::find()->where(['product_id' => $this->id])->sum('quantity');
        $this->updateAttributes(['quantity' => empty($quantity) ? 0 : $quantity]);
    }

    /**
     * ????????????OPTION
     */
    public function initCategoryOption()
    {
        $this->clearOptionAndSku();

        // ?????????????????????????????????OPTION
        if (!$this->has_option) {
            // ???????????????SKU
            $this->initSku();
            return;
        }

        // ??????????????????, ????????????????????????OPTION
        if (!$this->is_platform) {
            return;
        }

        // ??????????????????OPTION
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
     * ??????SKU
     */
    public function initSku()
    {
        $this->clearSku();

        // ?????????????????????, ???????????????SKU
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

        // ??????OPTION, ??????SKU
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
     * ????????????????????????SKU
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
        $text .= '<th width="150">??????</th>';
        $text .= '<th width="150">?????????</th>';
        $text .= '<th width="150">????????????</th>';
        //$text .= '<th width="150">??????</th>';
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

