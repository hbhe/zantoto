<?php

namespace rest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $createTimeStart;
    public $createTimeEnd;

    public $order_tags;
    public $skill_tags;
    public $brand_tags;
    // public $statuses;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'days', 'headcount'], 'integer'],
            [['id', 'mobile', 'name', 'area_parent_id', 'area_id', 'title', 'detail', 'start_date', 'created_at', 'updated_at', 'memo', 'reason'], 'safe'],
            [['amount'], 'number'],

            [['order_tags', 'skill_tags', 'brand_tags', 'status'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \kartik\daterange\DateRangeBehavior::className(),
                'attribute' => 'created_at',
                'dateStartFormat' => false,
                'dateEndFormat' => false,
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();
        $query->alias('order');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);
        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            Yii::error([__METHOD__, __LINE__, $this->errors]);
            $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->order_tags)) {
            $this->order_tags = explode(',', $this->order_tags);
            $query->joinWith(['orderTags orderTags'], true, 'LEFT JOIN')
                ->andWhere(['orderTags.id' => $this->order_tags]);
        }

        if (!empty($this->skill_tags)) {
            $this->skill_tags = explode(',', $this->skill_tags);
            $query->joinWith(['skillTags skillTags'], true, 'LEFT JOIN')
                ->andWhere(['skillTags.id' => $this->skill_tags]);
        }

        $query->distinct();

        $query->with('memberOrders', 'orderTags', 'skillTags', 'brandTags', 'areaCode', 'parentAreaCode');

        // grid filtering conditions
        $query->andFilterWhere([
            'member_id' => $this->member_id,
            'amount' => $this->amount,
            'days' => $this->days,
            'headcount' => $this->headcount,
            'order.status' => $this->status,
            'area_parent_id' => $this->area_parent_id,
            'area_id' => $this->area_id,
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'name', $this->name])
//            ->andFilterWhere(['>=', 'DATE(created_at)', $this->createTimeStart])
//            ->andFilterWhere(['<=', 'DATE(created_at)', $this->createTimeEnd])
            ->andFilterWhere(['>=', 'DATE(order.created_at)', $this->createTimeStart])
            ->andFilterWhere(['<=', 'DATE(order.created_at)', $this->createTimeEnd])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'memo', $this->memo])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }

}

/*
        if (!empty($this->order_tags)) {
            $this->order_tags = explode(',', $this->order_tags);
            $query->joinWith(['directOrderTags directOrderTags'], true, 'INNER JOIN')
                ->andWhere(['directOrderTags.tag_id' => $this->order_tags]);
        }

        if (!empty($this->skill_tags)) {
            $this->skill_tags = explode(',', $this->skill_tags);
            $query->joinWith(['directSkillTags directSkillTags'], true, 'INNER JOIN')
                ->andWhere(['directSkillTags.tag_id' => $this->skill_tags]);
        }
*/
