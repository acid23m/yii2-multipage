<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 15:41
 */

namespace multipage\models;

use multipage\components\GeoIp;
use yii\base\InvalidConfigException;

/**
 * Class Process.
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Process
{
    /**
     * Replace markers.
     * @static
     * @param null|string $text Raw content with markers
     * @return string Changed text with content which depends on url get-parameters
     * @throws InvalidConfigException
     */
    public static function replaceMarkers(?string $text): string
    {
        if ($text === '' || $text === null) {
            return '';
        }

        // all rules
        $rules = Parameter::find()
            ->with([
                'marker' => function (MarkerQuery $query) {
                    $query->published();
                }
            ])
            ->published()
            ->all();

        /** @var GeoIp $geoip */
        $geoip = \Yii::$app->get('geoip');


        // replace markers with certain values in 1st iteration
        foreach ($rules as &$rule) {
            $marker_name = $rule->marker->name; // mark name to replace
//            $marker_default_value = $rule->marker->text; // default value for mark
            $marker_replacement = $rule->replacement; // mark value to replace
            // language dependency
            if (empty($rule->language)) {
                $is_language_suitable = true;
            } else {
                $is_language_suitable = $rule->language === \Yii::$app->language;
            }

            switch ($rule->type) {
                case $rule::TYPE_URL_QUERY:
                    $get_name = $rule->query_name; // parameter name to find
                    $get_value = $rule->query_value; // parameter value to find
                    $get_value_now = \Yii::$app->getRequest()->get($get_name); // current parameter value in url

                    if ($get_value_now !== null) { // if parameter exists in url
                        // define operator
                        switch ($rule->operator) {
                            case $rule::OPERATOR_EQUALLY: // direct entry
                                if ($get_value_now === $get_value && $is_language_suitable) {
                                    // replace marker with certain value
                                    $text = \str_replace($marker_name, $marker_replacement, $text);
                                }
                                break;

                            case $rule::OPERATOR_CONTAINS: // not direct entry
                                if (\strpos($get_value_now, $get_value) !== false && $is_language_suitable) {
                                    // replace marker with certain value
                                    $text = \str_replace($marker_name, $marker_replacement, $text);
                                }
                                break;
                        }
                    }
                    unset($get_name, $get_value, $get_value_now);
                    break;

                case $rule::TYPE_GEO_COUNTRY:
                    $country_id = $rule->country_id; // country id to find
                    // current country
                    try {
                        [$country_now, ,] = $geoip->getData();
                    } catch (\Throwable $e) {
                        $country_now = null;
                    }

                    if (!empty($country_id) && $country_now !== null && $country_id === $country_now['id'] && $is_language_suitable) {
                        // replace marker with certain value
                        $text = \str_replace($marker_name, $marker_replacement, $text);
                    }
                    unset($country_id, $country_now);
                    break;

                case $rule::TYPE_GEO_REGION:
                    $region_id = $rule->region_id; // region id to find
                    // current region
                    try {
                        [, $region_now,] = $geoip->getData();
                    } catch (\Throwable $e) {
                        $region_now = null;
                    }

                    if (!empty($region_id) && $region_now !== null && $region_id === $region_now['id'] && $is_language_suitable) {
                        // replace marker with certain value
                        $text = \str_replace($marker_name, $marker_replacement, $text);
                    }
                    unset($region_id, $region_now);
                    break;

                case $rule::TYPE_GEO_CITY:
                    $city_id = $rule->city_id; // city id to find
                    // current city
                    try {
                        [, , $city_now] = $geoip->getData();
                    } catch (\Throwable $e) {
                        $city_now = null;
                    }

                    if (!empty($city_id) && $city_now !== null && $city_id === $city_now['id'] && $is_language_suitable) {
                        // replace marker with certain value
                        $text = \str_replace($marker_name, $marker_replacement, $text);
                    }
                    unset($city_id, $city_now);
                    break;
            }
        }
        unset($rule, $marker_name, $marker_replacement);

        // replace markers with default values in 2nd iteration
        foreach ($rules as &$rule) {
            $marker_name = $rule->marker->name; // mark name to replace
            $marker_default_value = $rule->marker->text; // default value for mark

            $text = \str_replace($marker_name, $marker_default_value, $text);
        }
        unset($rules, $rule, $marker_name, $marker_default_value);

        // replace markers with default values in 3 iteration
        $markers = Marker::find()->published()->all();
        foreach ($markers as &$marker) {
            $text = \str_replace($marker->name, $marker->text, $text);
        }
        unset($markers, $marker);

        return $text;
    }

}
