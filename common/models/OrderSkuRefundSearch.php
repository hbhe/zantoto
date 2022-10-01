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
 * OrderSkuRefundSearch represents the model behind the search form of `common\models\OrderSkuRefund`.
 */
class OrderSkuRefundSearch extends OrderSkuRefund
{
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'buyer_id', 'order_sku_id', 'need_ship', 'handled_by', 'shipping_by', 'status'], 'integer'],
            [['order_id', 'product_id', 'mobile', 'nickname', 'refund_reason', 'refund_detail', 'handled_time', 'handled_memo', 'shipping_address', 'shipping_zipcode', 'shipping_name', 'shipping_mobile', 'shipping_time', 'shipping_memo', 'express_company', 'express_code', 'created_at', 'updated_at', 'image1', 'image2', 'image3'], 'safe'],
            [['refund_amount', 'refund_coupon'], 'number'],
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
        $query = OrderSkuRefund::find();

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
            'order_sku_id' => $this->order_sku_id,
            'need_ship' => $this->need_ship,
            'refund_amount' => $this->refund_amount,
            'refund_coupon' => $this->refund_coupon,
            'handled_by' => $this->handled_by,
            'handled_time' => $this->handled_time,
            'shipping_by' => $this->shipping_by,
            'shipping_time' => $this->shipping_time,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'product_id', $this->product_id])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['>=', 'DATE(created_at)', $this->createTimeStart])
            ->andFilterWhere(['<=', 'DATE(created_at)', $this->createTimeEnd])
            ->andFilterWhere(['like', 'refund_reason', $this->refund_reason])
            ->andFilterWhere(['like', 'refund_detail', $this->refund_detail])
            ->andFilterWhere(['like', 'handled_memo', $this->handled_memo])
            ->andFilterWhere(['like', 'shipping_address', $this->shipping_address])
            ->andFilterWhere(['like', 'shipping_zipcode', $this->shipping_zipcode])
            ->andFilterWhere(['like', 'shipping_name', $this->shipping_name])
            ->andFilterWhere(['like', 'shipping_mobile', $this->shipping_mobile])
            ->andFilterWhere(['like', 'shipping_memo', $this->shipping_memo])
            ->andFilterWhere(['like', 'express_company', $this->express_company])
            ->andFilterWhere(['like', 'express_code', $this->express_code]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
