<?php
/**
 *  @link http://github.com/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @author 57620133@qq.com
 */

namespace common\models;

use mohorev\file\UploadImageBehavior;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%seller}}".
 *
 * @property string $id
 * @property integer $member_id
 * @property integer $cat
 * @property integer $parent_cat
 * @property string $title
 * @property string $company
 * @property string $credit_code
 * @property string $area_parent_id
 * @property string $area_id
 * @property string $district_id
 * @property string $address
 * @property string $open_time
 * @property string $tel
 * @property string $detail
 * @property string $legal_person
 * @property string $legal_identity
 * @property string $business_licence_image
 * @property string $identity_face_image
 * @property string $identity_back_image
 * @property string $logo
 * @property string $logo_path
 * @property string $logo_base_url
 * @property integer $seller_status
 * @property string $seller_time
 * @property string $seller_reason
 * @property integer $sort_order
 * @property integer $status
 * @property integer $order_count_daily
 * @property integer $order_amount_daily
 * @property integer $order_count_total
 * @property integer $order_amount_total
 * @property string $created_at
 * @property string $updated_at
 */
class Shop extends \common\models\ActiveRecord
{
    public $logo_picture;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company', 'address', 'legal_identity', 'credit_code', 'area_parent_id', 'area_id', 'district_id', 'legal_person', 'business_licence_image', 'identity_face_image', 'identity_back_image',], 'required'],
            [['member_id', 'cat', 'parent_cat', 'seller_status', 'sort_order', 'status', 'order_count_daily', 'order_count_total'], 'integer'],
            [['order_amount_daily', 'order_amount_total'], 'number'],
            [['member_id', 'cat', 'parent_cat', 'seller_status', 'sort_order', 'status'], 'default', 'value' => 0],
            [['seller_time', 'created_at', 'updated_at'], 'safe'],
            [['id', 'credit_code', 'open_time', 'tel', 'company'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 128],
            [['seller_reason'], 'string', 'max' => 255],
            [['area_parent_id', 'area_id', 'district_id', 'legal_person'], 'string', 'max' => 16],
            [['detail'], 'string', 'max' => 1024],
            [['legal_identity'], 'string', 'max' => 20],
            [['member_id'], 'unique'],
            [['id'], 'unique'],

            [['business_licence_image', 'identity_face_image', 'identity_back_image',], 'image', ], // 'on' => ['insert', 'update'] 'extensions' => 'jpg, jpeg, gif, png',
            [['order_count_daily', 'order_amount_daily', 'order_count_total', 'order_amount_total'], 'default', 'value' => 0],
            [['logo_picture'], 'safe'],

        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],

            'id' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'id'
                ],
                'value' => self::generateSid(),
            ],

            'logo_picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'logo_picture',
                'pathAttribute' => 'logo_path',
                'baseUrlAttribute' => 'logo_base_url',
            ],

            'business_licence_image' => [
                'class' => UploadImageBehavior::class,
                'generateNewName' => function($file) { return md5(uniqid() . $file->name . rand(1, 1000)) . '.' . $file->extension; }, // form中多文件上传时用这个保证唯一性, 插件中的默然函数不行
                'instanceByName' => true, // 因为REST中想直接使用pay_filename=xxx.jpg, 不想用Trade[pay_filename]=xxx.jpg
                'attribute' => 'business_licence_image',
                //'scenarios' => ['insert', 'update'],
                'scenarios' => [self::SCENARIO_DEFAULT],
                //'placeholder' => '@storage/web/images/avatar.png',
                'path' => '@storage/web/images/seller', // 定义文件存放的目录
                'url' => '@storageUrl/images/seller',
                //'url' => '@backendUrl/img/share/{id}',
                'thumbs' => [ // 定义各种size的thumbs
                    'thumb' => ['width' => 100, 'height' => 100, 'quality' => 90],
                    //'preview' => ['width' => 200, 'height' => 200], // 'bg_color' => '000'
                ],
            ],

            'identity_face_image' => [
                'class' => UploadImageBehavior::class,
                'generateNewName' => function($file) { return md5(uniqid() . $file->name . rand(1, 1000)) . '.' . $file->extension; }, // form中多文件上传时用这个保证唯一性, 插件中的默然函数不行
                'instanceByName' => true, // 因为REST中想直接使用pay_filename=xxx.jpg, 不想用Trade[pay_filename]=xxx.jpg
                'attribute' => 'identity_face_image',
                //'scenarios' => ['insert', 'update'],
                'scenarios' => [self::SCENARIO_DEFAULT],
                //'placeholder' => '@storage/web/images/avatar.png',
                'path' => '@storage/web/images/seller', // 定义文件存放的目录
                'url' => '@storageUrl/images/seller',
                //'url' => '@backendUrl/img/share/{id}',
                'thumbs' => [ // 定义各种size的thumbs
                    'thumb' => ['width' => 100, 'height' => 100, 'quality' => 90],
                    //'preview' => ['width' => 200, 'height' => 200], // 'bg_color' => '000'
                ],
            ],

            'identity_back_image' => [
                'class' => UploadImageBehavior::class,
                'generateNewName' => function($file) { return md5(uniqid() . $file->name . rand(1, 1000)) . '.' . $file->extension; }, // form中多文件上传时用这个保证唯一性, 插件中的默然函数不行
                'instanceByName' => true, // 因为REST中想直接使用pay_filename=xxx.jpg, 不想用Trade[pay_filename]=xxx.jpg
                'attribute' => 'identity_back_image',
                //'scenarios' => ['insert', 'update'],
                'scenarios' => [self::SCENARIO_DEFAULT],
                //'placeholder' => '@storage/web/images/avatar.png',
                'path' => '@storage/web/images/seller', // 定义文件存放的目录
                'url' => '@storageUrl/images/seller',
                //'url' => '@backendUrl/img/share/{id}',
                'thumbs' => [ // 定义各种size的thumbs
                    'thumb' => ['width' => 100, 'height' => 100, 'quality' => 90],
                    //'preview' => ['width' => 200, 'height' => 200], // 'bg_color' => '000'
                ],
            ],

        ];
    }

    static public function generateSid()
    {
        return uniqid() . sprintf("%02d", rand(0, 99)) . '-s';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '会员ID',
            'parent_cat' => '一级分类',
            'cat' => '主营业务',
            'title' => '店铺名称',
            'company' => '公司名称',
            'credit_code' => '信用代码',
            'area_parent_id' => '省份',
            'area_id' => '城市',
            'district_id' => '区县',
            'address' => '详细地址',
            'open_time' => '营业时间',
            'tel' => '营业电话',
            'detail' => '店铺详情',
            'legal_person' => '法人姓名',
            'legal_identity' => '法人身份证号',
            'business_licence_image' => '营业执照',
            'identity_face_image' => '法人身份证正面',
            'identity_back_image' => '法人身份证反面',
            'logo' => '店铺Logo',
            'seller_status' => '认证状态',
            'sellerStatus' => '认证状态',
            'seller_time' => '店铺认证时间',
            'seller_reason' => '店铺拒绝理由',
            'sort_order' => '排序',
            'status' => '店铺状态',
            'statusString' => '店铺状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'parentCatString' => '一级分类',
            'catString' => '主营业务',
            'catPathString' => '主营业务',
            'sellerStatusString' => '认证状态',
            'logoUrl' => '店铺Logo',
            'logoThumbUrl' => '店铺Logo',
            'licenceUrl' => '营业执照',
            'licenceThumbUrl' => '营业执照',
            'faceUrl' => '法人身份证正面',
            'faceThumbUrl' => '法人身份证正面',
            'backUrl' => '法人身份证反面',
            'backThumbUrl' => '法人身份证反面',
            'logo_picture' => '店铺Logo',
            'order_count_daily' => '日订单数',
            'order_amount_daily' => '日订单金额',
            'order_count_total' => '累计订单数',
            'order_amount_total' => '累计订单金额',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (ArrayHelper::getValue($changedAttributes, 'seller_status') != $this->seller_status) {
            $this->member->updateAttributes(['is_seller' => $this->seller_status == self::SELLER_STATUS_OK ? 1 : 0]);
        }
    }


    /**
     * @inheritdoc
     * @return ShopQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ShopQuery(get_called_class());
    }

    public function getCatString()
    {
        return ArrayHelper::getValue($this, 'category.name');
    }

    public function getCatPathString()
    {
        $arr[] = ArrayHelper::getValue($this, 'parentCategory.name');
        $arr[] = ArrayHelper::getValue($this, 'category.name');
        return implode('-', $arr);
    }

    public function getCategory()
    {
        return $this->hasOne(OutletCategory::className(), ['id' => 'cat']);
    }

    public function getParentCategory()
    {
        return $this->hasOne(OutletCategory::className(), ['id' => 'parent_cat']);
    }

    public function getShopStatusString()
    {
        return ArrayHelper::getValue(self::getShopStatusArray(), $this->seller_status);
    }

    const SELLER_STATUS_WAIT = 0;
    const SELLER_STATUS_OK = 1;
    const SELLER_STATUS_REFUSED = 2;

    public static function getShopStatusArray()
    {
        return [
            self::SELLER_STATUS_WAIT => '待审核',
            self::SELLER_STATUS_OK => '已通过',
            self::SELLER_STATUS_REFUSED => '已拒绝',
        ];
    }

    public function getStatusString()
    {
        return ArrayHelper::getValue(self::getStatusArray(), $this->status);
    }

    const STATUS_OK = 0;
    const STATUS_STOP = 1;
    public static function getStatusArray()
    {
        return [
            self::STATUS_OK => '开店中',
            self::STATUS_STOP => '已关店',
        ];
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'area_id']);
    }

    public function getParentAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'area_parent_id']);
    }

    public function getDistrictAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'district_id']);
    }

    public function getAreaCodeName()
    {
        return ArrayHelper::getValue($this, 'areaCode.name');
    }

    public function getParentAreaCodeName()
    {
        return ArrayHelper::getValue($this, 'parentAreaCode.name');
    }

    public function getDistrictAreaCodeName()
    {
        return ArrayHelper::getValue($this, 'districtAreaCode.name');
    }

    public function getDefaultAvatarImageUrl()
    {
        return Yii::getAlias('@storageUrl/images/male.png');
    }

    public function getLogoUrl()
    {
        if (empty($this->logo_base_url) && empty($this->logo_path)) {
            return null;
        }
        if (empty($this->logo_path)) {
            return $this->logo_base_url;
        }
        return $this->logo_base_url . '/' . $this->logo_path;
    }

    public function getLogoPath()
    {
        if (empty($this->logo_base_url) && empty($this->logo_path)) {
            return null;
        }
        $path = Yii::getAlias('@storage/web/source') . '/' . $this->logo_path;
        if (file_exists($path)) {
            return $path;
        }
        $path = Yii::getAlias('@backend/web/image-samples/people') . '/' . $this->logo_path;
        if (file_exists($path)) {
            return $path;
        }
        return Yii::getAlias('@storage/web/images/male.png');
    }

    public function getLogoThumbUrl()
    {
        return $this->getLogoUrl();
    }


    public function getLicenceUrl() {
        return $this->getUploadUrl('business_licence_image');
    }

    public function getLicenceThumbUrl() {
        return $this->getThumbUploadUrl('business_licence_image', 'thumb');
    }

    public function getFaceUrl() {
        return $this->getUploadUrl('identity_face_image');
    }

    public function getFaceThumbUrl() {
        return $this->getThumbUploadUrl('identity_face_image', 'thumb');
    }

    public function getBackUrl() {
        return $this->getUploadUrl('identity_back_image');
    }

    public function getBackThumbUrl() {
        return $this->getThumbUploadUrl('identity_back_image', 'thumb');
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['logo']);
        unset($fields['logo_path']);
        unset($fields['logo_base_url']);
        unset($fields['business_licence_image']);
        unset($fields['identity_face_image']);
        unset($fields['identity_back_image']);

        $fields['parentAreaCodeName'] = 'parentAreaCodeName';
        $fields['areaCodeName'] = 'areaCodeName';
        $fields['districtAreaCodeName'] = 'districtAreaCodeName';
        $fields['sellerStatusString'] = 'sellerStatusString';
        $fields['logoUrl'] = 'logoUrl';
        $fields['licenceUrl'] = 'licenceUrl';
        $fields['faceUrl'] = 'faceUrl';
        $fields['backUrl'] = 'backUrl';
        $fields['catString'] = 'catString';
        $fields['catPathString'] = 'catPathString';

        if (Yii::$app->request->isConsoleRequest || Yii::$app->user->isGuest || Yii::$app->user->id != $this->member_id) {
            // 不公开的隐私信息
            unset($fields['company']);
            unset($fields['credit_code']);
            unset($fields['legal_person']);
            unset($fields['legal_identity']);
            unset($fields['licenceUrl']);
            unset($fields['faceUrl']);
            unset($fields['backUrl']);
        }
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'member';

        return $fields;
    }

}
