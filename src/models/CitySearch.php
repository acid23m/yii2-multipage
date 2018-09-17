<?php

namespace multipage\models;

use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;
use yii\data\ActiveDataProvider;

/**
 * CitySearch represents the model behind the search form about `multipage\models\City`.
 */
final class CitySearch extends City
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
            [['id', 'region_id', 'country_id'], 'integer'],
            [['name_ru', 'name_en'], 'safe'],
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
        $query = City::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['id', 'region_id', 'country_id', 'name_ru', 'name_en', 'latitude', 'longitude'],
                'enableMultiSort' => true,
                'defaultOrder' => [
                    'name_en' => SORT_ASC,
                    'region_id' => SORT_ASC,
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
            'city.id' => $this->id,
            'city.region_id' => $this->region_id,
            'city.country_id' => $this->country_id
        ]);

        $query->andFilterWhere(['like', 'city.name_ru', $this->name_ru])
            ->andFilterWhere(['like', 'city.name_en', $this->name_en])
            ->andFilterWhere(['like', 'city.latitude', $this->latitude])
            ->andFilterWhere(['like', 'city.longitude', $this->longitude]);

        return $dataProvider;
    }

}
