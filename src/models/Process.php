<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 15:41
 */

namespace multipage\models;

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
     */
    public static function replaceMarkers(?string $text): string
    {
        if ($text === '' || $text === null) {
            return '';
        }

        $cache_id = md5('multipage_content' . $text . \Yii::$app->getRequest()->getQueryString());

        return \Yii::$app->getCache()->getOrSet($cache_id, function () use (&$text) {
            // all rules
            $rules = Parameter::find()
                ->with([
                    'marker' => function (MarkerQuery $query) {
                        $query->published();
                    }
                ])
                ->published()
                ->all();

            // replace markers with certain values in 1st iteration
            foreach ($rules as &$rule) {
                // find get parameter (utm)
                $marker_name = $rule->marker->name; // mark name to replace
                $marker_default_value = $rule->marker->text; // default value for mark
                $marker_replacement = $rule->replacement; // mark value to replace
                $get_name = $rule->name; // parameter name to find
                $get_value = $rule->text; // parameter value to find
                $get_value_now = \Yii::$app->getRequest()->get($get_name); // current parameter value in url

                if ($get_value_now !== null) { // if parameter exists in url
                    // define operator
                    switch ($rule->operator) {
                        case $rule::OPERATOR_EQUALLY: // direct entry
                            if ($get_value_now === $get_value) {
                                // replace marker with certain value
                                $text = \str_replace($marker_name, $marker_replacement, $text);
                            }
                            break;

                        case $rule::OPERATOR_CONTAINS: // not direct entry
                            if (\strpos($get_value_now, $get_value) !== false) {
                                // replace marker with certain value
                                $text = \str_replace($marker_name, $marker_replacement, $text);
                            }
                            break;
                    }
                } else { // if parameter not exists in url
                    // replace marker with default value
                    $text = \str_replace($marker_name, $marker_default_value, $text);
                }
            }
            unset($rule);

            // replace markers with default values in 2nd iteration
            foreach ($rules as &$rule) {
                $marker_name = $rule->marker->name; // mark name to replace
                $marker_default_value = $rule->marker->text; // default value for mark

                $text = str_replace($marker_name, $marker_default_value, $text);
            }
            unset($rules, $rule);

            return $text;
        }, 30);
    }

}
