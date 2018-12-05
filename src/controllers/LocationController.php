<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.09.18
 * Time: 22:49
 */

namespace multipage\controllers;

use multipage\components\GeoIp;
use multipage\models\City;
use multipage\models\Country;
use multipage\models\CountryQuery;
use multipage\models\GeoUpdater;
use multipage\models\Region;
use multipage\models\RegionQuery;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class LocationController.
 *
 * @package multipage\controllers
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class LocationController extends Controller
{
    /**
     * Upper case first letter.
     * @param string $str
     * @return string
     */
    protected function ucfirst(string $str): string
    {
        $enc = \Yii::$app->charset;

        return \mb_strtoupper(\mb_substr($str, 0, 1, $enc), $enc) .
            \mb_substr($str, 1, \mb_strlen($str, $enc), $enc);
    }

    /**
     * Find region for select box.
     * @param null|string $q Search query
     * @return array
     */
    public function actionSearchRegion($q = null): array
    {
        $lang = GeoUpdater::getGeoInfoLanguage();

        /** @var array $out [result => [id => option, text => label]] */
        $out = ['results' => []];

        $data = Region::find()
            ->with([
                'country' => function (CountryQuery $query) {
                    $query->addSelect(['{{country}}.[[id]]', '{{country}}.[[name_ru]]', '{{country}}.[[name_en]]']);
                }
            ])
            ->select(['{{region}}.[[id]]', '{{region}}.[[country_id]]', '{{region}}.[[iso]]', '{{region}}.[[name_ru]]', '{{region}}.[[name_en]]'])
            ->orFilterWhere(['like', '{{region}}.[[iso]]', $q])
            ->orFilterWhere(['like', '{{region}}.[[name_ru]]', $q])
            ->orFilterWhere(['like', '{{region}}.[[name_ru]]', $this->ucfirst($q)])
            ->orFilterWhere(['like', '{{region}}.[[name_en]]', $q])
            ->limit(20)
            ->all();

        foreach ($data as &$item) {
            $out['results'][] = [
                'id' => $item->id,
                'text' => $item->{"name_$lang"} . ' (' . $item->country->{"name_$lang"} . ')'
            ];
        }
        unset($data, $item);

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return $out;
    }

    /**
     * Find city for select box.
     * @param null|string $q Search query
     * @return array
     */
    public function actionSearchCity($q = null): array
    {
        $lang = GeoUpdater::getGeoInfoLanguage();

        /** @var array $out [result => [id => option, text => label]] */
        $out = ['results' => []];

        $data = City::find()
            ->with([
                'region' => function (RegionQuery $query) {
                    $query->addSelect(['{{region}}.[[id]]', '{{region}}.[[name_ru]]', '{{region}}.[[name_en]]']);
                },
                'country' => function (CountryQuery $query) {
                    $query->addSelect(['{{country}}.[[id]]', '{{country}}.[[name_ru]]', '{{country}}.[[name_en]]']);
                }
            ])
            ->select(['{{city}}.[[id]]', '{{city}}.[[region_id]]', '{{city}}.[[country_id]]', '{{city}}.[[name_ru]]', '{{city}}.[[name_en]]'])
            ->orFilterWhere(['like', '{{city}}.[[name_ru]]', $q])
            ->orFilterWhere(['like', '{{city}}.[[name_ru]]', $this->ucfirst($q)])
            ->orFilterWhere(['like', '{{city}}.[[name_en]]', $q])
            ->limit(20)
            ->all();

        foreach ($data as &$item) {
            $out['results'][] = [
                'id' => $item->id,
                'text' => $item->{"name_$lang"} . ' / ' . $item->region->{"name_$lang"} . ' (' . $item->country->{"name_$lang"} . ')'
            ];
        }
        unset($data, $item);

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return $out;
    }

    /**
     * Update user location.
     * @param string $sender
     * @param null|string|int $gl_country_id
     * @param null|string|int $gl_region_id
     * @param null|string|int $gl_city_id
     * @return Response
     * @throws \yii\base\InvalidCallException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($sender, $gl_country_id = null, $gl_region_id = null, $gl_city_id = null): Response
    {
        $return_url = StringHelper::base64UrlDecode($sender);

        $country_id = (int) $gl_country_id;
        $region_id = (int) $gl_region_id;
        $city_id = (int) $gl_city_id;

//        $country = Country::findOne($country_id);
//        $region = Region::findOne($region_id);
//        $city = City::findOne($city_id);
        $data = $this->autoFill($country_id, $region_id, $city_id);

        /** @var GeoIp $geoip */
        $geoip = \Yii::$app->get(\multipage\Module::GEOIP_COMPONENT_ID);

//        $location = $geoip->packData(\Yii::$app->getRequest()->getUserIP(), $country, $region, $city);
        $location = $geoip->packData(\Yii::$app->getRequest()->getUserIP(), ...$data);

        $geoip->setCookieData($location);
        $geoip->setSessionData($location);

        return $this->redirect($return_url);
    }

    /**
     * Automatically fill null values.
     * @param int $country_id
     * @param int $region_id
     * @param int $city_id
     * @return array [country, region, city]
     */
    protected function autoFill(int $country_id, int $region_id, int $city_id): array
    {
        $country = Country::findOne($country_id);
        $region = Region::findOne($region_id);
        $city = City::findOne($city_id);

        // define country
        if ($country === null) {
            if ($region !== null) {
                $country = $region->country;
            } elseif ($city !== null) {
                $country = $city->country;
            }
        }
        // define region
        if ($region === null) {
            if ($city !== null) {
                $region = $city->region;
            } elseif ($country !== null) {
                $region = $country->getRegions()->one();
            }
        }
        // define city
        if ($city === null) {
            if ($region !== null) {
                $city = $region->getCities()->one();
            } elseif ($country !== null) {
                $r = $country->getRegions()->one();
                $city = $r->getCities()->one();
            }
        }

        return [$country, $region, $city];
    }

}
