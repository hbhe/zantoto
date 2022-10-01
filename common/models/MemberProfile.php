<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%member_profile}}".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $is_real_name
 * @property string $identity
 * @property string $card_id
 * @property string $card_name
 * @property string $card_branch
 * @property string $card_bank
 * @property string $alipay_id
 * @property string $alipay_name
 * @property string $ext
 */
class MemberProfile extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'is_real_name'], 'integer'],
            [['member_id', 'is_real_name'], 'default', 'value' => 0],
            [['ext'], 'string'],
            [['identity'], 'string', 'max' => 20],
            [['card_id', 'card_name', 'card_branch', 'card_bank', 'alipay_id', 'alipay_name'], 'string', 'max' => 64],
            [['member_id'], 'unique'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
            /*
            [
                'class' => DynamicAttributeBehavior::className(),
                'storageAttribute' => 'ext',
                'allowRandomDynamicAttribute' => false,
                'saveDynamicAttributeDefaults' => false,
                'dynamicAttributeDefaults' => [
                    'ext_has_im' => 0,
                    'powerArray' => [],
                ],
            ],
            */
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户ID',
            'is_real_name' => '已实名',
            'identity' => '身份证',
            'card_id' => '银行卡号',
            'card_name' => '户名',
            'card_branch' => '支行',
            'card_bank' => '开户行',
            'alipay_id' => '支付宝账号',
            'alipay_name' => '支付宝账号姓名',
            'ext' => '扩展信息',
        ];
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

}