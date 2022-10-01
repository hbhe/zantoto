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
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tid', 'mobile', 'nickname', 'pay_time', 'shipping_address', 'shipping_zipcode', 'shipping_name', 'shipping_mobile', 'shipping_time', 'express_company', 'express_code', 'created_at', 'updated_at', 'memo', 'is_platform'], 'safe'],
            [['member_id', 'buyer_id', 'quantity', 'pay_method', 'status', 'has_refund', 'is_giftkey'], 'safe'],
            [['total_amount', 'shipping_fee', 'pay_amount',], 'number'],
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

        // grid filtering conditions
        $query->andFilterWhere([
            'member_id' => $this->member_id,
            'buyer_id' => $this->buyer_id,
            'is_platform' => $this->is_platform,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'shipping_fee' => $this->shipping_fee,
            'pay_amount' => $this->pay_amount,
            'pay_method' => $this->pay_method,
            'pay_time' => $this->pay_time,
            'shipping_time' => $this->shipping_time,
            'status' => $this->status,
            'has_refund' => $this->has_refund,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'tid', $this->tid])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['>=', 'DATE(order.created_at)', $this->createTimeStart])
            ->andFilterWhere(['<=', 'DATE(order.created_at)', $this->createTimeEnd])
            ->andFilterWhere(['like', 'shipping_address', $this->shipping_address])
            ->andFilterWhere(['like', 'shipping_zipcode', $this->shipping_zipcode])
            ->andFilterWhere(['like', 'shipping_name', $this->shipping_name])
            ->andFilterWhere(['like', 'shipping_mobile', $this->shipping_mobile])
            ->andFilterWhere(['like', 'express_company', $this->express_company])
            ->andFilterWhere(['like', 'express_code', $this->express_code])
            ->andFilterWhere(['like', 'memo', $this->memo]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
