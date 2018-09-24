<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.09.18
 * Time: 0:30
 */

use kartik\select2\Select2;
use multipage\models\City;
use multipage\models\Country;
use multipage\models\GeoUpdater;
use multipage\models\Region;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var \yii\web\View $this */
/** @var \multipage\widgets\GeoLocation $widget */
$widget = $this->context;
$lang = GeoUpdater::getGeoInfoLanguage();

switch ($widget->question) {

    case $widget::ASK_COUNTRY:
        $index = $widget::ASK_COUNTRY;
        $selector = Select2::widget([
            'name' => 'gl_country_id',
            'value' => $widget->getLocation()[$index]['id'] ?? null,
            'data' => Country::getDropdownList(),
            'theme' => Select2::THEME_BOOTSTRAP
        ]);
        $label = \Yii::t('multipage', 'vasha strana');
        break;

    case $widget::ASK_REGION:
        $index = $widget::ASK_REGION;
        $init_value = '';
        if (isset($widget->getLocation()[$index]['id'])) {
            $region = Region::find()
                ->with('country')
                ->where(['id' => $widget->getLocation()[$index]['id']])
                ->one();

            $init_value = $region->{"name_$lang"} . ' (' . $region->country->{"name_$lang"} . ')';
        }
        $selector = Select2::widget([
            'name' => 'gl_region_id',
            'value' => $widget->getLocation()[$index]['id'] ?? null,
            'initValueText' => $init_value,
            'theme' => Select2::THEME_BOOTSTRAP,
            'pluginOptions' => [
                'minimumInputLength' => 2,
                'ajax' => [
                    'url' => Url::to([\multipage\Module::DEFAULT_ID . '/location/search-region']),
                    'dataType' => 'json',
                    'delay' => 2000,
                    'cache' => true,
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }')
            ]
        ]);
        $label = \Yii::t('multipage', 'vash region');
        break;

    case $widget::ASK_CITY:
        $index = $widget::ASK_CITY;
        $init_value = '';
        if (isset($widget->getLocation()[$index]['id'])) {
            $city = City::find()
                ->with(['country', 'region'])
                ->where(['id' => $widget->getLocation()[$index]['id']])
                ->one();
            $init_value = $city->{"name_$lang"} . ' / ' . $city->region->{"name_$lang"} . ' (' . $city->country->{"name_$lang"} . ')';
        }
        $selector = Select2::widget([
            'name' => 'gl_city_id',
            'value' => $widget->getLocation()[$index]['id'] ?? null,
            'initValueText' => $init_value,
            'theme' => Select2::THEME_BOOTSTRAP,
            'pluginOptions' => [
                'minimumInputLength' => 2,
                'ajax' => [
                    'url' => Url::to([\multipage\Module::DEFAULT_ID . '/location/search-city']),
                    'dataType' => 'json',
                    'delay' => 2000,
                    'cache' => true,
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }')
            ]
        ]);
        $label = \Yii::t('multipage', 'vash gorod');
        break;

    default:
        $index = $widget::ASK_COUNTRY;
        $selector = Select2::widget([
            'name' => 'gl_country_id',
            'value' => $widget->getLocation()[$index]['id'] ?? null,
            'data' => Country::getDropdownList(),
            'theme' => Select2::THEME_BOOTSTRAP
        ]);
        $label = \Yii::t('multipage', 'vasha strana');
}

$link_text = $widget->getLocation()[$index]["name_$lang"] ?? \Yii::t('multipage', 'vybrat mestopolozhenie');
?>

<?php
$this->registerCss('
#geolocation-select-box {
    box-sizing: border-box;
    position: absolute;
    top: 10px;
    left: -300px;
    width: 300px;
    height: auto;
    color: #333;
    background-color: #eee;
    border: #ccc 1px solid;
    border-radius: 0 4px 4px 0;
    padding: 5px 10px 10px 10px;
    margin: 0;
    transition: .2s;
}
#geolocation-select-box.active {
    left: 0;
}
#geolocation-select-box a,
#geolocation-select-box button {
    outline: none;
}
#geolocation-select-box .geolocation-select-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: sans-serif;
    font-size: 12px;
    margin-bottom: 5px;
}
#geolocation-select-box .geolocation-select-top .geolocation-select-close,
#geolocation-select-box .geolocation-select-top .geolocation-select-close:hover,
#geolocation-select-box .geolocation-select-top .geolocation-select-close:focus {
    color: #333;
    text-decoration: none;
}
#geolocation-select-box .geolocation-select-action {
    display: flex;
    justify-content: space-around;
    align-items: center;
    font-family: sans-serif;
    font-size: 14px;
    padding-top: 5px;
}
#geolocation-select-box .geolocation-select-action .geolocation-select-ok {
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 5px 10px;
}
#geolocation-select-box .geolocation-select-action .geolocation-select-close,
#geolocation-select-box .geolocation-select-action .geolocation-select-close:hover,
#geolocation-select-box .geolocation-select-action .geolocation-select-close:focus {
    color: #333;
    text-decoration: none;
}
', [], 'gl_css')
?>

<?php
$this->registerJs('
(function (d) {
    "use strict";
    
    const geolocationCurrentItems = d.querySelectorAll(".geolocation-current-item");
    
    const geolocationSelectBox = d.getElementById("geolocation-select-box");
    
    geolocationCurrentItems.forEach(item => {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            
            geolocationSelectBox.classList.toggle("active");
        }, false);
    });
    
    const geolocationSelectCloses = geolocationSelectBox.querySelectorAll(".geolocation-select-close");
    
    geolocationSelectCloses.forEach(item => {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            
            geolocationSelectBox.classList.toggle("active");
        }, false);
    })
})(document);
', $this::POS_END, 'gl_js')
?>


<span class="geolocation-current">
    <?= Html::a($link_text, '#', ['class' => 'geolocation-current-item']) ?>
</span>

<?php if ($widget->show_selector): ?>
    <div id="geolocation-select-box">
        <?= Html::beginForm([
            \multipage\Module::DEFAULT_ID . '/location/update',
            'sender' => StringHelper::base64UrlEncode(\Yii::$app->getRequest()->getAbsoluteUrl())
        ], 'get') ?>

        <div class="geolocation-select-top">
            <span><?= $label ?></span>
            <a class="geolocation-select-close" href="#">&times;</a>
        </div>

        <div class="geolocation-select-item">
            <?= $selector ?>
        </div>

        <div class="geolocation-select-action">
            <button class="geolocation-select-ok" type="submit">
                <?= \Yii::t('multipage', 'primenit') ?>
            </button>
            <a class="geolocation-select-close" href="#">
                <?= \Yii::t('multipage', 'zakrit') ?>
            </a>
        </div>

        <?= Html::endForm() ?>
    </div>
<?php endif ?>
