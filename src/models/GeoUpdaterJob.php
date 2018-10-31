<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 31.10.18
 * Time: 12:38
 */

namespace multipage\models;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class GeoUpdaterJob.
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class GeoUpdaterJob extends BaseObject implements JobInterface
{
    public const TYPE_INFO = 1;
    public const TYPE_DATA = 2;

    /**
     * @var int
     */
    public $type;

    /**
     * @param Queue $queue
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\db\Exception
     */
    public function execute($queue): void
    {
        switch ($this->type) {
            // update geo data
            case self::TYPE_INFO:
                GeoUpdater::getInfo();
                break;
            // sxgeo data
            case self::TYPE_DATA:
                GeoUpdater::getData();
                break;
        }
    }

}
