<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 2:17
 */

namespace multipage\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ParameterSearch represents the model behind the search form about `multipage\models\Parameter`.
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class ParameterSearch extends Parameter
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
            [['id', 'marker_id', 'type'], 'integer'],
            [['language', 'replacement'], 'safe'],
            [['status'], 'boolean']
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
     *
     * @param array $params
     * @return ActiveDataProvider
     * @throws InvalidArgumentException
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Parameter::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['id', 'marker_id', 'language', 'type', 'status'],
                'enableMultiSort' => true,
                'defaultOrder' => ['marker_id' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%parameter}}.[[id]]' => $this->id,
            '{{%parameter}}.[[marker_id]]' => $this->marker_id,
            '{{%parameter}}.[[language]]' => $this->language,
            '{{%parameter}}.[[type]]' => $this->type,
            '{{%parameter}}.[[status]]' => $this->status
        ]);

        $query->andFilterWhere(['like', '{{%parameter}}.[[replacement]]', $this->replacement]);

        return $dataProvider;
    }

}
