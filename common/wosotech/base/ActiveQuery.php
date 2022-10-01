<?php
namespace common\wosotech\base;

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
     * @param null $month, null 表示本月, 为'2018-09'表示指定某年某月
     * @return $this
     */
    public function month($month = null)
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
