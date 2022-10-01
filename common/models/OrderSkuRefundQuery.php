<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderSkuRefund]].
 *
 * @see OrderSkuRefund
 */
class OrderSkuRefundQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return OrderSkuRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderSkuRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
