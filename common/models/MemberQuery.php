<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

/**
 * This is the ActiveQuery class for [[Member]].
 *
 * @see Member
 */
class MemberQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['status' => Member::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     * @return Member[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Member|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function isSeller()
    {
        $this->andWhere(['is_seller' => 1]);
        return $this;
    }

}
