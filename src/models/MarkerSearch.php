<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 1:52
 */

namespace multipage\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MarkerSearch represents the model behind the search form about `multipage\models\Marker`.
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class MarkerSearch extends Marker
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
            [['id'], 'integer'],
            [['name', 'text'], 'safe'],
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
        $query = Marker::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['id', 'name', 'status'],
                'defaultOrder' => ['name' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%marker}}.[[id]]' => $this->id,
            '{{%marker}}.[[status]]' => $this->status
        ]);

        $query->andFilterWhere(['like', '{{%marker}}.[[name]]', $this->name])
            ->andFilterWhere(['like', '{{%marker}}.[[text]]', $this->text]);

        return $dataProvider;
    }

}
