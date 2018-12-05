<?php

namespace multipage\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RegionSearch represents the model behind the search form about `multipage\models\Region`.
 */
final class RegionSearch extends Region
{
    /**
     * {@inheritdoc}
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'country_id'], 'integer'],
            [['iso', 'name_ru', 'name_en', 'timezone'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidArgumentException
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Region::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['id', 'country_id', 'iso', 'name_ru', 'name_en', 'timezone'],
                'enableMultiSort' => true,
                'defaultOrder' => [
                    'iso' => SORT_ASC,
                    'country_id' => SORT_ASC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{region}}.[[id]]' => $this->id,
            '{{region}}.[[country_id]]' => $this->country_id,
        ]);

        $query->andFilterWhere(['like', '{{region}}.[[iso]]', $this->iso])
            ->andFilterWhere(['like', '{{region}}.[[name_ru]]', $this->name_ru])
            ->andFilterWhere(['like', '{{region}}.[[name_en]]', $this->name_en])
            ->andFilterWhere(['like', '{{region}}.[[timezone]]', $this->timezone]);

        return $dataProvider;
    }

}
