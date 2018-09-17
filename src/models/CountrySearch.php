<?php

namespace multipage\models;

use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;
use yii\data\ActiveDataProvider;

/**
 * CountrySearch represents the model behind the search form about `multipage\models\Country`.
 */
final class CountrySearch extends Country
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'latitude' => AttributeTypecastBehavior::TYPE_FLOAT,
                    'longitude' => AttributeTypecastBehavior::TYPE_FLOAT
                ],
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => false,
                'typecastAfterFind' => false
            ]
        ];
    }

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
            [['id'], 'integer'],
            [['iso', 'continent', 'name_ru', 'name_en', 'timezone'], 'safe'],
            [['latitude', 'longitude'], 'number']
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
        $query = Country::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['id', 'iso', 'continent', 'name_ru', 'name_en', 'timezone', 'latitude', 'longitude'],
                'enableMultiSort' => true,
                'defaultOrder' => ['iso' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'country.id' => $this->id
        ]);

        $query->andFilterWhere(['like', 'country.iso', $this->iso])
            ->andFilterWhere(['like', 'country.continent', $this->continent])
            ->andFilterWhere(['like', 'country.name_ru', $this->name_ru])
            ->andFilterWhere(['like', 'country.name_en', $this->name_en])
            ->andFilterWhere(['like', 'country.timezone', $this->timezone])
            ->andFilterWhere(['like', 'country.latitude', $this->latitude])
            ->andFilterWhere(['like', 'country.longitude', $this->longitude]);

        return $dataProvider;
    }

}
