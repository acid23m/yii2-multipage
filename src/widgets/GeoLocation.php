<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.09.18
 * Time: 0:22
 */

namespace multipage\widgets;

use multipage\components\GeoIp;
use multipage\models\City;
use yii\base\Widget;

/**
 * Class GeoLocation.
 *
 * @package multipage\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class GeoLocation extends Widget
{
    public const DEFAULT_CITY = 'Moscow';

    public const ASK_COUNTRY = 1;
    public const ASK_REGION = 2;
    public const ASK_CITY = 3;

    /**
     * @var null|\SplFixedArray Geo IP data
     */
    private $location;

    /**
     * @var string Default city in ru or en.
     */
    public $default_city = self::DEFAULT_CITY;
    /**
     * @var int Question type
     */
    public $question = self::ASK_CITY;
    /**
     * @var bool Show or not popup window with location selector
     */
    public $show_selector = true;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        /** @var GeoIp $geoip */
        $geoip = \Yii::$app->get(\multipage\Module::GEOIP_COMPONENT_ID);

        // check session
        if ($this->location === null) { // if location is not defined
            $this->location = $geoip->getSessionData();
        }
        // check cookie
        if ($this->location === null) { // if location is not defined
            $this->location = $geoip->getCookieData();
            $geoip->setSessionData($this->location);
        }
        // define by ip
        if ($this->location === null) {
            $this->location = $geoip->getIpData();
            $geoip->setCookieData($this->location);
            $geoip->setSessionData($this->location);
        }
        // set default
        if ($this->location === null) { // if location is not defined
            $city = City::find()
                ->with(['region', 'country'])
                ->orWhere(['name_ru' => $this->default_city])
                ->orWhere(['name_en' => $this->default_city])
                ->one();
            $region = $country = null;
            if ($city !== null) {
                $region = $city->region;
                $country = $city->country;
            }

            $this->location = $geoip->packData(\Yii::$app->getRequest()->getUserIP(), $country, $region, $city);
            $geoip->setCookieData($this->location);
            $geoip->setSessionData($this->location);
        }
    }

    /**
     * User location.
     * @return null|\SplFixedArray
     */
    public function getLocation(): ?\SplFixedArray
    {
        return $this->location;
    }

    /**
     * Render location selector.
     * @return string
     * @throws \yii\base\InvalidArgumentException
     */
    public function run(): string
    {
        return $this->render('geolocation');
    }

}
