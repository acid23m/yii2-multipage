<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 1:49
 */

namespace multipage\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Marker]].
 *
 * @see Marker
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class MarkerQuery extends ActiveQuery
{
    /**
     * Show only published items.
     * @return $this
     */
    public function published(): self
    {
        $this->andWhere(['{{marker}}.[[status]]' => Marker::STATUS_ACTIVE]);

        return $this;
    }

    /**
     * Show only draft items.
     * @return $this
     */
    public function draft(): self
    {
        $this->andWhere(['{{marker}}.[[status]]' => Marker::STATUS_NOT_ACTIVE]);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return Marker[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Marker|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
