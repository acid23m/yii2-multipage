<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 28.01.16
 * Time: 11:50
 */

namespace multipage\traits;

use yii\base\InvalidArgumentException;

/**
 * Model helpers.
 *
 * @package multipage\traits
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
trait Model
{
    /**
     * Selection list.
     * @param string $_list
     * @return \Closure
     * @throws InvalidArgumentException
     */
    public function getList(string $_list): \Closure
    {
        if (!\property_exists($this, $_list)) {
            throw new InvalidArgumentException('List not found.');
        }

        $list = $this->$_list;

        if (!\is_iterable($list)) {
            throw new InvalidArgumentException('List must be an associative array.');
        }

        /**
         * @param bool $associative
         * @return iterable
         */
        return function (bool $associative = true) use ($list): iterable {
            return $associative ? $list : \array_keys($list);
        };
    }

    /**
     * Show some properties in one string.
     * @param string $template {property} will be replaced
     * @return string
     */
    public function asString(string $template): string
    {
        \preg_match_all('/{([\w_]+)}/', $template, $matches);

        $search = $matches[0];
        $replace = function () use ($matches) {
            foreach ($matches[1] as $property) {
                try {
                    yield $this->$property;
                } catch (\Throwable $e) {
                    \Yii::debug($e->getMessage(), __CLASS__);
                }
            }
        };

        return \str_replace($search, \iterator_to_array($replace()), $template);
    }

}
