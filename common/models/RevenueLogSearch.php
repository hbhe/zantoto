<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RevenueLogSearch represents the model behind the search form of `common\models\RevenueLog`.
 */
class RevenueLogSearch extends RevenueLog
{
    public $createTimeStart;
    public $createTimeEnd;

    public $mobile;
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'kind', 'status', 'cashout_status'], 'integer'],
            [['title', 'memo', 'created_at', 'updated_at', 'order_time'], 'safe'],
            [['amount', 'order_amount'], 'number'],
            [['mobile', 'name'], 'safe'],
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
        $query = RevenueLog::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'kind' => $this->kind,
            'amount' => $this->amount,
            'order_amount' => $this->order_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order_time' => $this->order_time,
            'status' => $this->status,
            'cashout_status' => $this->cashout_status,
        ]);

        if (!empty($this->mobile)) {
            $query->joinWith('member member');
            $query->andFilterWhere(['like', 'member.mobile', $this->mobile]);
        }

        if (!empty($this->name)) {
            $query->joinWith('member member');
            $query->andFilterWhere(['like', 'member.name', $this->name]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'memo', $this->memo]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
