<?php

namespace multipage\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Region]].
 *
 * @see Region
 */
final class RegionQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Region[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Region|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
