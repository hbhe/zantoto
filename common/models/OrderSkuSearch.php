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
 * OrderSkuSearch represents the model behind the search form of `common\models\OrderSku`.
 */
class OrderSkuSearch extends OrderSku
{
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'buyer_id', 'quantity', 'status'], 'integer'],
            [['order_id', 'product_id', 'sku_code', 'main_image', 'main_image_thumb', 'option_value_names', 'image', 'created_at', 'updated_at', 'title'], 'safe'],
            [['price', 'amount'], 'number'],
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
        $query = OrderSku::find();

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
            'price' => $this->price,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'product_id', $this->product_id])
            ->andFilterWhere(['like', 'sku_code', $this->sku_code])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'main_image', $this->main_image])
            ->andFilterWhere(['like', 'main_image_thumb', $this->main_image_thumb])
            ->andFilterWhere(['like', 'option_value_names', $this->option_value_names])
            ->andFilterWhere(['like', 'image', $this->image]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
