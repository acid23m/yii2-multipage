<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 16.09.18
 * Time: 0:55
 */

use kartik\select2\Select2;
use multipage\models\Region;
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
if (!empty($model->region)) {
    $region = Region::find()
        ->with('country')
        ->where(['iso' => $model->region])
        ->one();
    $init_value = $region->{"name_$lang"} . ' (' . $region->country->{"name_$lang"} . ')';
}
?>

<?= $form->field($model, 'region')->widget(Select2::class, [
    'initValueText' => $init_value,
    'theme' => Select2::THEME_BOOTSTRAP,
    'options' => [
        'prompt' => ''
    ],
    'pluginOptions' => [
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['parameter/search-region']),
            'dataType' => 'json',
            'delay' => 2000,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'allowClear' => true
    ]
]) ?>

<?= $form->field($model, 'country')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'city')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'query_name')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'query_value')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'operator')->hiddenInput(['value' => ''])->label(false) ?>
