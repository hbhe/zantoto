<?php

namespace rest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MemberSearch represents the model behind the search form of `common\models\Member`.
 */
class MemberSearch extends Member
{
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status',], 'integer'],
            [['sid', 'mobile', 'name', 'email', 'avatar_path', 'avatar_base_url', 'created_at', 'updated_at', 'logged_at', 'pid', 'is_star', 'is_seller', 'is_outlet'], 'safe'],

            [['id', 'createTimeStart', 'createTimeEnd', 'power_daily', ], 'safe'],
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

    public function search($params)
    {
        $query = Member::find()->active();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                //'defaultOrder' => ['updated_at' => SORT_DESC],
            ]
        ]);

        $this->load($params);
        $this->load($params, '');

        //$this->status = Member::STATUS_ACTIVE;

        if (!$this->validate()) {
            Yii::error([__METHOD__, __LINE__, $this->errors]);
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sid' => $this->sid,
            'pid' => $this->pid,
            'status' => $this->status,
            'is_star' => $this->is_star,
            'is_seller' => $this->is_seller,
            'is_outlet' => $this->is_outlet,
        ]);

        $query->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['>=', 'DATE(created_at)', $this->createTimeStart])
            ->andFilterWhere(['<=', 'DATE(created_at)', $this->createTimeEnd])
            ->andFilterWhere(['like', 'email', $this->email]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }

}
