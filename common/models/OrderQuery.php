<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

/**
 * Class OrderQuery
 * @package common\models
 */
class OrderQuery extends ActiveQuery
{
    public function valid()
    {
        return $this->andWhere(['status' => [Order::STATUS_AUCTION, Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_CONFIRM]]);
    }

    public function invalid()
    {
        return $this->andWhere(['status' => [Order::STATUS_CLOSED]]);
    }

    public function done()
    {
        return $this->andWhere(['status' => [Order::STATUS_CONFIRM]]);
    }

    /**
     * @inheritdoc
     * @return Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
