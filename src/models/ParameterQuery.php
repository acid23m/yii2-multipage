<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 2:03
 */

namespace multipage\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Parameter]].
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class ParameterQuery extends ActiveQuery
{
    /**
     * Show only published items.
     * @return $this
     */
    public function published(): self
    {
        $this->andWhere(['{{%parameter}}.[[status]]' => Parameter::STATUS_ACTIVE]);

        return $this;
    }

    /**
     * Show only draft items.
     * @return $this
     */
    public function draft(): self
    {
        $this->andWhere(['{{%parameter}}.[[status]]' => Parameter::STATUS_NOT_ACTIVE]);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return Parameter[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Parameter|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
