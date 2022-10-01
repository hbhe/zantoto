<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%member_address}}".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $mobile
 * @property string $area_parent_id
 * @property string $area_id
 * @property string $district_id
 * @property string $address
 * @property integer $is_default
 * @property string $created_at
 * @property string $updated_at
 */
class MemberAddress extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_address}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'is_default'], 'integer'],
            [['member_id', 'is_default'], 'default', 'value' => 0],
            [['name', 'mobile'], 'string', 'max' => 64],
            [['district_id', 'area_parent_id', 'area_id'], 'string', 'max' => 16],
            [['address'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
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

    public function beforeSave($insert)
    {
        if ($this->is_default == 1) {
            MemberAddress::updateAll(['is_default' => 0], ['member_id' => $this->member_id]);
        }
        return parent::beforeSave($insert);
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

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['parentAreaCodeName'] = 'parentAreaCodeName';
        $fields['areaCodeName'] = 'areaCodeName';
        $fields['districtAreaCodeName'] = 'districtAreaCodeName';
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '会员ID',
            'name' => '姓名',
            'mobile' => '联系电话',
            'address' => '收货地址',
            'area_parent_id' => '省份',
            'area_id' => '地市',
            'district_id' => '区省',
            'is_default' => '默认',
            'parentAreaCodeName' => '省份',
            'areaCodeName' => '地市',
            'districtAreaCodeName' => '区省',
        ];
    }
}
