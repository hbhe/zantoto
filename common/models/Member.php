<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property string $sid
 * @property string $username
 * @property string $mobile
 * @property string $name
 * @property string $nickname
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_plain
 * @property string $password_hash
 * @property string $email
 * @property integer $status
 * @property string $gender
 * @property string $area_parent_id
 * @property string $area_id
 * @property integer $age
 * @property string $avatar_path
 * @property string $avatar_base_url
 * @property string $created_at
 * @property string $updated_at
 * @property string $logged_at
 * @property integer $pid
 * @property integer $status_bind
 * @property integer $status_audit
 * @property string $openid
 * @property string $identity
 * @property string $weixin_number
 * @property string $weixin_qrcode_path
 * @property string $weixin_qrcode_base_url
 * @property string $card_id
 * @property string $card_name
 * @property string $card_branch
 * @property string $card_bank
 * @property string $alipay_id
 * @property string $alipay_name
 * @property string $balance_revenue
 * @property integer $balance_coupon
 * @property integer $is_real_name
 * @property integer $is_seller
 *
 * @property MemberProfile $memberProfile
 * @property MemberAddress[] $memberAddresses
 * @property Order[] $buyOrders
 * @property Order[] $sellerOrders
 * @property RevenueLog[] $revenueLogs
 */
class Member extends \common\models\ActiveRecord implements IdentityInterface
{
    const ROOT_ID = 999999999;

    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 0;

    /*
    "picture": {
        "path": "\\1\\WgIKo8cwclWSEW0Pw7hmP3qdaiVNg9A-.jpg",
        "base_url": "http://127.0.0.1/zantoto/storage/web/source",
        "type": "image/jpeg",
        "size": 4416,
        "name": null,
        "order": 0,
        "timestamp": 1542292441
    },
    */
    public $picture; // 头像

    public $verify_code;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'auth_key', 'access_token', 'sid', 'username'], 'safe'],
            [['id', 'status', 'age', 'pid', 'is_seller',], 'integer'],
            [['balance_revenue',], 'number'],
            [['balance_revenue'], 'default', 'value' => 0],
            [['created_at', 'updated_at', 'logged_at'], 'safe'],
            [['sid', 'nickname', 'access_token', 'password_plain', 'password_hash'], 'string', 'max' => 64],
            [['username', 'name', 'auth_key', 'email',], 'string', 'max' => 32],
            [['mobile', 'area_parent_id', 'area_id'], 'string', 'max' => 16],
            [['avatar_path', 'avatar_base_url',], 'string', 'max' => 255],
            [['gender'], 'string', 'max' => 4],
            [['sid'], 'unique'],
            [['mobile'], 'unique'],
            [['id'], 'unique'],
            [['id', 'status', 'age', 'pid', 'is_seller',], 'default', 'value' => 0],
            [['picture', 'verify_code'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],

            'sid' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'sid'
                ],
                'value' => self::generateSid(),
            ],

            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],

            'access_token' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token'
                ],
                'preserveNonEmptyValues' => true,
                'value' => function () {
                    return Yii::$app->getSecurity()->generateRandomString(40);
                }
            ],

            'picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'avatar_path',
                'baseUrlAttribute' => 'avatar_base_url',
            ],

        ];
    }


    static public function generateSid()
    {
        return Yii::$app->getSecurity()->generateRandomString(16) . uniqid();
    }

    public static function generateId()
    {
        while (true) {
            $id = rand(100000000, 999999998);
            if (self::findOne($id)) {
                Yii::error([__METHOD__, __LINE__, 'generateId() repeat', $id]);
                continue;
            }
            return $id;
        }
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
            'id' => '用户ID',
            'sid' => '加密ID',
            'username' => '账号',
            'mobile' => '手机',
            'name' => '姓名',
            'nickname' => '昵称',
            'auth_key' => 'Auth密钥',
            'access_token' => 'Token',
            'password_plain' => '密码',
            'password_hash' => '密码',
            'email' => 'Email',
            'status' => '状态',
            'gender' => '性别',
            'area_parent_id' => '省份',
            'area_id' => '城市',
            'age' => '年龄',
            'avatar_path' => '头像',
            'avatar_base_url' => '头像',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'logged_at' => '最近登录',
            'pid' => '上级ID',
            'balance_revenue' => '账户余额',
            'is_seller' => '是卖家',
            'avatarImageUrl' => '头像',
            'picture' => '头像',
        ];
    }

    /**
     * @inheritdoc
     * @return MemberQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MemberQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['access_token' => $token])
            ->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['mobile' => $username], ['username' => $username]])
            ->one();
    }

    public function isMe()
    {
        return (!Yii::$app->user->isGuest) && Yii::$app->user->id == $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function getGenderOption()
    {
        return [
            'm' => '男',
            'f' => '女',
        ];
    }

    public function getGenderString()
    {
        return ArrayHelper::getValue(self::getGenderOption(), $this->gender);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    public function getPublicIdentity()
    {
        return $this->nickname;
    }

    public function getRoleType()
    {
        $arr = [];
        if ($this->is_seller) {
            $arr[] = '卖家';
        }
        return implode(',', $arr);
    }

    public function getPassword()
    {
        return $this->password_plain;
    }

    public function setPassword($password)
    {
        $this->password_plain = $password;
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function getDefaultAvatarImageUrl()
    {
        return Yii::getAlias('@storageUrl/images/male.png');
    }

    public function getAvatarImageUrl()
    {
        if (empty($this->avatar_base_url) && empty($this->avatar_path)) {
            return $this->getDefaultAvatarImageUrl();
        }
        if (empty($this->avatar_path)) {
            return $this->avatar_base_url;
        }
        return $this->avatar_base_url . '/' . $this->avatar_path;
    }

    public function getAvatarImagePath()
    {
        if (empty($this->avatar_base_url) && empty($this->avatar_path)) {
            return Yii::getAlias('@storage/web/images/male.png');
        }
        $path = Yii::getAlias('@storage/web/source') . '/' . $this->avatar_path;
        if (file_exists($path)) {
            return $path;
        }
        $path = Yii::getAlias('@backend/web/image-samples/people') . '/' . $this->avatar_path;
        if (file_exists($path)) {
            return $path;
        }
        return Yii::getAlias('@storage/web/images/male.png');
    }

    public function getAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'area_id']);
    }

    public function getParentAreaCode()
    {
        return $this->hasOne(AreaCode::className(), ['id' => 'area_parent_id']);
    }

    public function getAreaCodeName()
    {
        return empty($this->areaCode->name) ? '' : $this->areaCode->name;
    }

    public function getParentAreaCodeName()
    {
        return empty($this->parentAreaCode->name) ? '' : $this->parentAreaCode->name;
    }

    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => '正常',
            self::STATUS_NOT_ACTIVE => '冻结',
        ];
    }

    public function getStatusString()
    {
        return ArrayHelper::getValue(self::getStatusArray(), $this->status);
    }

    public static function getStatusBindArray()
    {
        return [
            0 => '未绑定',
            1 => '已绑定',
        ];
    }

    public function getImPassword()
    {
        return md5($this->password_hash);
    }

    public function getParent()
    {
        return $this->hasOne(Member::className(), ['id' => 'pid']);
    }

    public function getChildrenIds()
    {
        return Member::find()->select(['id'])
            ->andWhere(['pid' => $this->id])
            ->column();
    }

    public function getParentName()
    {
        return ArrayHelper::getValue($this, 'parent.name');
    }

    public function getParentMobile()
    {
        return ArrayHelper::getValue($this, 'parent.mobile');
    }

    public function getMemberProfile()
    {
        return $this->hasOne(MemberProfile::className(), ['member_id' => 'id']);
    }

    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['member_id' => 'id']);
    }

    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }

    public function getHiddenMobile()
    {
        return $this->mobile;
    }

    public function getTodayKeyInCache($key)
    {
        return [__METHOD__, $this->id, $key];
    }

    public function getTodayValueInCache($key, $defaultValue)
    {
        $key = $this->getTodayKeyInCache($key);
        $value = Yii::$app->cache->get($key);
        return $value === false ? $defaultValue : $value;
    }

    public function setTodayValueInCache($key, $value)
    {
        $key = $this->getTodayKeyInCache($key);
        $rest_seconds = strtotime('tomorrow midnight') - time(); // 到今晚24点还剩xx秒
        return Yii::$app->cache->set($key, $value, $rest_seconds);
    }

    public function getMemberAddresses()
    {
        return $this->hasMany(MemberAddress::className(), ['member_id' => 'id']);
    }

    public function getBuyOrders()
    {
        return $this->hasMany(Order::className(), ['buyer_id' => 'id']);
    }

    public function getSellOrders()
    {
        return $this->hasMany(Order::className(), ['member_id' => 'id']);
    }

    public function getRevenueLogs()
    {
        return $this->hasMany(RevenueLog::className(), ['member_id' => 'id']);
    }

    public function afterDelete()
    {
        foreach ($this->auths as $model) {
            $model->delete();
        }
        if ($this->shop) {
            $this->shop->delete();
        }
        if ($this->memberProfile) {
            $this->memberProfile->delete();
        }
        parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $model = new MemberProfile();
            $this->link('memberProfile', $model);
        }
    }

    public function fields()
    {
        $fields = parent::fields();
        unset(
            $fields['password_hash'],
            $fields['password_plain'],
            $fields['auth_key'],
            $fields['status_bind'],
            $fields['status_audit'],
            $fields['status_audit'],
            $fields['auth_key'],
            $fields['avatar_path'],
            $fields['avatar_base_url'],
            $fields['weixin_qrcode_path'],
            $fields['weixin_qrcode_base_url'],
            $fields['area_parent_id'],
            $fields['area_id'],
            $fields['picture']
        );

        $fields['avatarImageUrl'] = 'avatarImageUrl';

        if (!$this->isMe() && $this->scenario != 'login') {
            // 不公开的隐私信息
            unset($fields['access_token']);
            unset($fields['openid']);
            unset($fields['email']);
            unset($fields['identity']);
            unset($fields['weixin_number']);
            unset($fields['weixinImageUrl']);
            unset($fields['card_id']);
            unset($fields['card_name']);
            unset($fields['card_branch']);
            unset($fields['card_bank']);
            unset($fields['alipay_id']);
            unset($fields['alipay_name']);
            unset($fields['balance_revenue']);
            unset($fields['balance_fish']);
            unset($fields['balance_coupon']);
            unset($fields['mobile']);
            unset($fields['sid']);
            unset($fields['age']);
            unset($fields['is_real_name']);
            unset($fields['name']);
            unset($fields['username']);
            unset($fields['gender']);
            unset($fields['updated_at']);
        }

        if ($this->scenario == static::SCENARIO_VIEW) {
        }

        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        return $fields;
    }

}
