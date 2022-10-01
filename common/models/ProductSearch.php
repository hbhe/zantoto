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
use yii\helpers\StringHelper;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductSearch extends Product
{
    public $q;
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spu_id', 'brand_id', 'title', 'sub_title', 'main_image', 'detail', 'memo', 'category_path', 'created_at', 'updated_at', 'listing_time', 'delisting_time', 'is_platform',], 'safe'],
            [['member_id', 'category_id', 'category_id1', 'category_id2', 'category_id3', 'quantity', 'sold_volume', 'shipping', 'has_option', 'sort_order', 'status_listing', 'total_rate_score', 'status'], 'integer'],
            [['price', 'cost_price', 'market_price',], 'number'],
            [['q'], 'safe'],
            [['q'], 'filter', 'filter' => 'trim'],
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
        $query = Product::find();

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

        if (!empty($this->q)) {
            $keys = StringHelper::explode($this->q, ' ', true, true);
            $fields = ['title', 'sub_title', 'brand_id'];
            $conditions = ['or'];
            foreach ($keys as $key) {
                foreach ($fields as $field) {
                    $conditions[] = ['like', $field, $key];
                }
            }
            $query->andFilterWhere($conditions);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'member_id' => $this->member_id,
            'is_platform' => $this->is_platform,
            'category_id' => $this->category_id,
            'category_id1' => $this->category_id1,
            'category_id2' => $this->category_id2,
            'category_id3' => $this->category_id3,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'market_price' => $this->market_price,
            'shipping' => $this->shipping,
            'has_option' => $this->has_option,
            'sort_order' => $this->sort_order,
            'status_listing' => $this->status_listing,
            'total_rate_score' => $this->total_rate_score,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'category_path', $this->category_path])
            ->andFilterWhere(['like', 'spu_id', $this->spu_id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['>=', 'DATE(created_at)', $this->createTimeStart])
            ->andFilterWhere(['<=', 'DATE(created_at)', $this->createTimeEnd])
            ->andFilterWhere(['like', 'sub_title', $this->sub_title]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
