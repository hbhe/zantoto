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
 * RateSearch represents the model behind the search form of `common\models\Rate`.
 */
class RateSearch extends Rate
{
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'buyer_id', 'score', 'is_anonymous', 'status', 'is_hidden', 'sort_order', 'order_sku_id'], 'integer'],
            [['order_id', 'product_id', 'product_title', 'content', 'ip', 'nickname', 'created_at', 'updated_at'], 'safe'],
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
        $query = Rate::find();

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
            'buyer_id' => $this->buyer_id,
            'score' => $this->score,
            'is_anonymous' => $this->is_anonymous,
            'is_hidden' => $this->is_hidden,
            'status' => $this->status,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'order_sku_id' => $this->order_sku_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'product_title', $this->product_title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'nickname', $this->nickname]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
