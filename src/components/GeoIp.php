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
use yii\web\Cookie;

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
    public const GEOLOCATION_STORAGE_ID = '__geolocation';

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
     * Pack geo location data.
     * @param string $ip
     * @param Country $country
     * @param Region|null $region
     * @param City|null $city
     * @return \SplFixedArray
     */
    public function packData(string $ip, ?Country $country, ?Region $region, ?City $city): \SplFixedArray
    {
        $info = new \SplFixedArray(4);

        $info[0] = $ip;
        $info[1] = $country !== null ? ArrayHelper::toArray($country) : null;
        $info[2] = $region !== null ? ArrayHelper::toArray($region) : null;
        $info[3] = $city !== null ? ArrayHelper::toArray($city) : null;

        return $info;
    }

    /**
     * Get IP geo information.
     * @param null|string $ip
     * @return null|\SplFixedArray [ip, country, region, city]
     */
    public function getIpData(?string $ip = null): ?\SplFixedArray
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

        $country = Country::findOne((int) $data['country']['id']);
        $region = Region::findOne((int) $data['region']['id']);
        $city = City::findOne((int) $data['city']['id']);

        return $this->packData($ip, $country, $region, $city);
    }

    /**
     * Get IP geo information from session.
     * @return null|\SplFixedArray
     * @see packData()
     * @see setSessionData()
     */
    public function getSessionData(): ?\SplFixedArray
    {
        return \Yii::$app->getSession()->get(self::GEOLOCATION_STORAGE_ID);
    }

    /**
     * Set IP geo information to session.
     * @param null|\SplFixedArray $data
     * @return bool
     * @see packData()
     * @see getSessionData()
     */
    public function setSessionData(?\SplFixedArray $data): bool
    {
        if ($data === null) {
            return false;
        }

        \Yii::$app->getSession()->set(self::GEOLOCATION_STORAGE_ID, $data);

        return true;
    }

    /**
     * Get IP geo information from cookie.
     * @return null|\SplFixedArray
     * @see packData()
     * @see setCookieData()
     */
    public function getCookieData(): ?\SplFixedArray
    {
        return \Yii::$app->getRequest()->getCookies()->getValue(self::GEOLOCATION_STORAGE_ID);
    }

    /**
     * Set IP geo information to cookie.
     * @param null|\SplFixedArray $data
     * @param array $cookie_params
     * @return bool
     * @throws \yii\base\InvalidCallException
     * @see packData()
     * @see getCookieData()
     */
    public function setCookieData(?\SplFixedArray $data, array $cookie_params = []): bool
    {
        if ($data === null) {
            return false;
        }

        $c_params = [
            'name' => self::GEOLOCATION_STORAGE_ID,
            'value' => $data,
            'expire' => time() + 2592000,
            'httpOnly' => true,
            'secure' => true
        ];

        $cookie = new Cookie(
            ArrayHelper::merge($c_params, $cookie_params)
        );

        \Yii::$app->getResponse()->getCookies()->add($cookie);

        return true;
    }

}
