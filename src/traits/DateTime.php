<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 11.09.17
 * Time: 15:27
 */

namespace multipage\traits;

/**
 * Date and time utilities.
 *
 * @package multipage\traits
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
trait DateTime
{
    /**
     * @return string Current time
     */
    public static function getNow(): string
    {
        $now = new \DateTime('now', new \DateTimeZone(\Yii::$app->timeZone));

        return $now->format(STANDARD_DATETIME_FORMAT);
    }

}
