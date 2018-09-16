<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 16.09.18
 * Time: 23:02
 */

namespace multipage\components;


use multipage\models\City;
use multipage\models\Country;
use multipage\models\GeoUpdater;
use multipage\models\Region;
use multipage\models\SxGeo;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Define geo data by IP.
 *
 * @package multipage\components
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 * @link https://sypexgeo.net/ru/docs/
 */
class GeoIp extends Component
{
    public const DB_FILE = 'SxGeoCity.dat';

    /**
     * @var SxGeo
     */
    protected $sxgeo;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        try {
            $db = \Yii::getAlias(GeoUpdater::DATA_DIR . '/' . self::DB_FILE);
            $this->sxgeo = new SxGeo($db, SxGeo::SXGEO_BATCH | SxGeo::SXGEO_MEMORY);
        } catch (\Throwable $e) {
            \Yii::warning($e->getMessage());
        }
    }

    /**
     * Get IP geo information.
     * @param null|string $ip
     * @return null|\SplFixedArray [country, region, city]
     */
    public function getData(?string $ip = null): ?\SplFixedArray
    {
        if ($this->sxgeo === null) {
            return null;
        }

        if ($ip === null) {
            $ip = \Yii::$app->getRequest()->getUserIP();
        }

        $data = $this->sxgeo->getCityFull($ip);

        if ($data === false) {
            return null;
        }

        try {
            $info = new \SplFixedArray(3);
            $info[0] = ArrayHelper::toArray(Country::findOne((int) $data['country']['id']));
            $info[1] = ArrayHelper::toArray(Region::findOne((int) $data['region']['id']));
            $info[2] = ArrayHelper::toArray(City::findOne((int) $data['city']['id']));
        } catch (\Throwable $e) {
            \Yii::warning($e->getMessage());

            return null;
        }

        return $info;
    }

}
