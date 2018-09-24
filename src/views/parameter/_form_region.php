<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 16.09.18
 * Time: 0:55
 */

use kartik\select2\Select2;
use multipage\models\GeoUpdater;
use multipage\models\Region;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var \yii\web\View $this */
/** @var \multipage\models\Parameter $model */
/** @var \yii\bootstrap\ActiveForm $form */

$lang = GeoUpdater::getGeoInfoLanguage();

$init_value = '';
if ($model->region_id !== null) {
    $region = Region::find()
        ->with('country')
        ->where(['id' => $model->region_id])
        ->one();

    $init_value = $region->{"name_$lang"} . ' (' . $region->country->{"name_$lang"} . ')';
}
?>

<?= $form->field($model, 'region_id')->widget(Select2::class, [
    'initValueText' => $init_value,
    'theme' => Select2::THEME_BOOTSTRAP,
    'options' => [
        'prompt' => ''
    ],
    'pluginOptions' => [
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['location/search-region']),
            'dataType' => 'json',
            'delay' => 2000,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'allowClear' => true
    ]
])->hint(
    \Yii::t('multipage', 'registro zavisimiy vvod') .
    ' ' . Html::a(\Yii::t('multipage', 'spisok regionov'), ['region/index'])
) ?>

<?= $form->field($model, 'country_id')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'city_id')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'query_name')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'query_value')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'operator')->hiddenInput(['value' => ''])->label(false) ?>
