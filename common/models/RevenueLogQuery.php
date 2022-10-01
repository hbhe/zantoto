<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

/**
 * This is the ActiveQuery class for [[Remark]].
 *
 * @see RevenueLog
 */
class RevenueLogQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return RevenueLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RevenueLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * 未结算的收益, 只有订单来的才存在未结算情况?
     * @param null $db
     * @return RevenueLog|array|null
     */
    public function uncleared($db = null)
    {
        return $this->andWhere(['kind' => [RevenueLog::KIND_COMMISSION_SALE]])
            ->andWhere(['<>', 'status', RevenueLog::STATUS_CLEARED]);
    }
}
