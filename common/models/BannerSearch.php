<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;

/**
 * BannerSearch represents the model behind the search form of `common\models\Banner`.
 */
class BannerSearch extends Banner
{
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cat', 'img_id', 'jump_type', 'app_function_id', 'second', 'sort_order', 'status'], 'integer'],
            [['title', 'detail', 'img_url', 'url', 'created_at', 'updated_at'], 'safe'],
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
        $query = Banner::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);
        $this->load($params, '');

        // 使用ActiveDataFilter, 支持过滤复杂条件,
        // 可以放在url中如?page=1&filter[or][0][cat]=1&filter[or][1][cat]=2这种，
        // 也可以在GET请求的body中传过来(但postman中好象设不了get的body参数?)，如 "filter": {"or": {...}},
        $filter = new ActiveDataFilter([
            'searchModel' => new self(),  //$this
        ]);
        if ($filter->load(Yii::$app->request->get())) {
            $filterCondition = $filter->build();
            if ($filterCondition === false) {
                // return $filter;
                Yii::error([__METHOD__, __LINE__, $filter->errors]);
                $query->where('0=1');
                return $dataProvider;
            }
            $query->andWhere($filterCondition);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            Yii::error([__METHOD__, __LINE__, $this->errors]);
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cat' => $this->cat,
            'img_id' => $this->img_id,
            'jump_type' => $this->jump_type,
            'app_function_id' => $this->app_function_id,
            'second' => $this->second,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'img_url', $this->img_url])
            ->andFilterWhere(['like', 'url', $this->url]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
