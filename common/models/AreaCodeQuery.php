<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use paulzi\adjacencyList\AdjacencyListQueryTrait;

/**
 * This is the ActiveQuery class for [[AreaCode]].
 *
 * @see AreaCode
 */
class AreaCodeQuery extends \yii\db\ActiveQuery
{
    use AdjacencyListQueryTrait;

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return AreaCode[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AreaCode|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
