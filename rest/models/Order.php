<?php

namespace rest\models;

use Yii;

class Order extends \common\models\Order
{
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getMemberOrders()
    {
        return $this->hasMany(MemberOrder::className(), ['order_id' => 'id']);
    }

    public function fields()
    {
        $fields = parent::fields();
        unset(
            $fields['memo'],
            $fields['updated_at']
        );
        $fields[] = 'firstTagImageUrl';
        $fields[] = 'parentAreaCodeName';
        $fields[] = 'areaCodeName';
        $fields[] = 'statusString';
        $fields[] = 'memberOrdersCount';
        // $fields[] = 'qrCodeImageUrl';
        $fields[] = 'shareQrCodeImageUrl';
        $fields[] = 'orderTagsString';
        $fields[] = 'skillTagsString';
        $fields[] = 'brandTagsString';
        $fields[] = 'order_tags';
        $fields[] = 'skill_tags';
        $fields[] = 'brand_tags';
        $fields[] = 'pictures';
        $fields[] = 'memberId';
        // $fields[] = 'photosDownloadUrls';
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'skillTags';
        $fields[] = 'brandTags';
        $fields[] = 'orderTags';
        $fields[] = 'memberOrders';
        $fields[] = 'member';
        $fields[] = 'photos';
        return $fields;
    }
}
