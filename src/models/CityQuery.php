<?php

namespace multipage\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[City]].
 *
 * @see City
 */
final class CityQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return City[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return City|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
