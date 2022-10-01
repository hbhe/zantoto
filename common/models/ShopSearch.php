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
 * ShopSearch represents the model behind the search form of `common\models\Shop`.
 */
class ShopSearch extends Shop
{
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'company', 'credit_code', 'area_parent_id', 'area_id', 'district_id', 'address', 'open_time', 'tel', 'detail', 'legal_person', 'legal_identity', 'business_licence_image', 'identity_face_image', 'identity_back_image', 'logo', 'seller_time', 'seller_reason', 'created_at', 'updated_at', 'order_count_daily', 'order_amount_daily', 'order_count_total', 'order_amount_total'], 'safe'],
            [['member_id', 'cat', 'seller_status', 'sort_order', 'status'], 'integer'],
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
        $query = Shop::find();

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
            'cat' => $this->cat,
            'seller_status' => $this->seller_status,
            'seller_time' => $this->seller_time,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'company', $this->company])
            ->andFilterWhere(['like', 'credit_code', $this->credit_code])
            ->andFilterWhere(['like', 'area_parent_id', $this->area_parent_id])
            ->andFilterWhere(['like', 'area_id', $this->area_id])
            ->andFilterWhere(['like', 'district_id', $this->district_id])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'open_time', $this->open_time])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'legal_person', $this->legal_person])
            ->andFilterWhere(['like', 'legal_identity', $this->legal_identity])
            ->andFilterWhere(['like', 'business_licence_image', $this->business_licence_image])
            ->andFilterWhere(['like', 'identity_face_image', $this->identity_face_image])
            ->andFilterWhere(['like', 'identity_back_image', $this->identity_back_image])
            ->andFilterWhere(['like', 'seller_reason', $this->seller_reason]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
