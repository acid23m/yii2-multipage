<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 16.09.18
 * Time: 0:55
 */

use kartik\select2\Select2;
use multipage\models\City;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var \yii\web\View $this */
/** @var \multipage\models\Parameter $model */
/** @var \yii\bootstrap\ActiveForm $form */

switch (\Yii::$app->language) {
    case 'ru':
        $lang = 'ru';
        break;
    case 'en':
        $lang = 'en';
        break;
    default:
        $lang = 'en';
}

$init_value = '';
if (!empty($model->city)) {
    $city = City::find()
        ->with(['country', 'region'])
        ->where(['name_en' => $model->city])
        ->one();
    $init_value = $city->{"name_$lang"} . ' / ' . $city->region->{"name_$lang"} . ' (' . $city->country->{"name_$lang"} . ')';
}
?>

<?= $form->field($model, 'city')->widget(Select2::class, [
    'initValueText' => $init_value,
    'theme' => Select2::THEME_BOOTSTRAP,
    'options' => [
        'prompt' => ''
    ],
    'pluginOptions' => [
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['parameter/search-city']),
            'dataType' => 'json',
            'delay' => 2000,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'allowClear' => true
    ]
]) ?>

<?= $form->field($model, 'country')->hiddenInput(['value' => '']) ?>

<?= $form->field($model, 'region')->hiddenInput(['value' => '']) ?>

<?= $form->field($model, 'query_name')->hiddenInput(['value' => '']) ?>

<?= $form->field($model, 'query_value')->hiddenInput(['value' => '']) ?>

<?= $form->field($model, 'operator')->hiddenInput(['value' => '']) ?>
