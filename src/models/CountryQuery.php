<?php

namespace multipage\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Country]].
 *
 * @see Country
 */
final class CountryQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Country[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Country|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
