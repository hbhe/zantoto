<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

/**
 * Class ActiveQuery
 * @package common\wosotech\base
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function today()
    {
        $this->andWhere(['>=', 'created_at', date('Y-m-d H:i:s', strtotime('today midnight'))]);
        return $this;
    }

    /**
     * @return $this
     */
    public function yesterday()
    {
        $this->andWhere(['>=', 'created_at', date('Y-m-d H:i:s', strtotime('yesterday midnight'))]);
        $this->andWhere(['<', 'created_at', date('Y-m-d H:i:s', strtotime('today midnight'))]);
        return $this;
    }

    /**
     * 本周
     * @return $this
     */
    public function week()
    {
        $date = date("Y-m-d");
        $this->andWhere(['yearweek(created_at)' => new \yii\db\Expression("yearweek(\"$date\")")]);
        return $this;
    }

    /**
     * 本月
     * 与theMonth()等效
     * @return $this
     */
    public function month()
    {
        $date = date("Y-m-d");
        $this->andWhere([
            'YEAR(created_at)' => new \yii\db\Expression("YEAR(\"$date\")"),
            'MONTH(created_at)' => new \yii\db\Expression("MONTH(\"$date\")"),
        ]);
        return $this;
    }

    /**
     * @param null $month , null 表示本月, 为'2018-09'表示指定某年某月
     * @return $this
     */
    public function theMonth($month = null)
    {
        if ($month === null) {
            $month = date('Y-m');
            $firstDay = date('Y-m-01', strtotime($month));
            $lastDay = date('Y-m-t', strtotime($month));
        } else {
            $firstDay = date('Y-m-01', strtotime($month));
            $lastDay = date('Y-m-t', strtotime($month));
        }
        $this->andWhere(['>=', 'created_at', $firstDay . " 00:00:00"]);
        $this->andWhere(['<=', 'created_at', $lastDay . " 23:59:59"]);
        return $this;
    }

}
