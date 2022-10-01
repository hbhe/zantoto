<?php

namespace rest\models;

use Yii;

class MemberOrder extends \common\models\MemberOrder
{
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

}
