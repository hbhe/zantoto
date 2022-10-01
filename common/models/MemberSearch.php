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
            [['id', 'status', 'age', 'pid', 'is_seller',], 'integer'], // 'is_star', 'is_outlet', 'status_bind', 'status_audit', 'balance_power', 'balance_fish', 'balance_coupon', 'is_real_name',
            [['sid', 'username', 'mobile', 'name', 'nickname', 'auth_key', 'access_token', 'password_plain', 'password_hash', 'email', 'gender', 'area_parent_id', 'area_id', 'avatar_path', 'avatar_base_url', 'created_at', 'updated_at', 'logged_at',], 'safe'], // 'openid', 'identity', 'weixin_number', 'card_id', 'card_name', 'card_branch', 'card_bank', 'alipay_id', 'alipay_name'
            [['balance_revenue'], 'number'],
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
        $query = Member::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    //'id' => SORT_DESC
                    'created_at' => SORT_DESC
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
            'status' => $this->status,
            'pid' => $this->pid,
            'is_seller' => $this->is_seller,
        ]);

        $query->andFilterWhere(['like', 'sid', $this->sid])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'area_parent_id', $this->area_parent_id])
            ->andFilterWhere(['like', 'area_id', $this->area_id])
            ->andFilterWhere(['>=', 'DATE(created_at)', $this->createTimeStart])
            ->andFilterWhere(['<=', 'DATE(created_at)', $this->createTimeEnd]);

        Yii::info($query->createCommand()->getRawSql());
        return $dataProvider;
    }
}
