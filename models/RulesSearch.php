<?php

namespace wdmg\robots\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\robots\models\Rules;

/**
 * RulesSearch represents the model behind the search form of `wdmg\robots\models\Rules`.
 */
class RulesSearch extends Rules
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'mode'], 'integer'],
            [['robot', 'rule'], 'string'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Rules::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'rule' => $this->rule,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);


        if ($this->robot !== "*")
            $query->andFilterWhere(['like', 'robot', $this->robot]);

        if ($this->mode !== "*")
            $query->andFilterWhere(['like', 'mode', $this->mode]);

        if ($this->status !== "*")
            $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
