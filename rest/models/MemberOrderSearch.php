<?php
namespace rest\models;

use Yii;
use yii\data\ActiveDataProvider;

class MemberOrderSearch extends MemberOrder
{
    public function rules()
    {
        return [
            [['id', 'order_id', 'created_at', 'updated_at', 'memo', 'reason', 'attachment', 'params', 'status'], 'safe'],
            [['member_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = MemberOrder::find();

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

        $query->with('member', 'order');

        // grid filtering conditions
        $query->andFilterWhere([
            'member_id' => $this->member_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
            'order_id' => $this->order_id,
        ]);

        $query->andFilterWhere(['like', 'attachment', $this->attachment])
            ->andFilterWhere(['like', 'params', $this->params]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }

}

