<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 16.09.18
 * Time: 0:55
 */

use kartik\select2\Select2;
use multipage\models\Country;

/** @var \yii\web\View $this */
/** @var \multipage\models\Parameter $model */
/** @var \yii\bootstrap\ActiveForm $form */
?>

<?= $form->field($model, 'country')->widget(Select2::class, [
    'data' => Country::getDropdownList(),
    'theme' => Select2::THEME_BOOTSTRAP,
    'options' => [
        'prompt' => ''
    ],
    'pluginOptions' => [
        'allowClear' => true
    ]
]) ?>

<?= $form->field($model, 'region')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'city')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'query_name')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'query_value')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'operator')->hiddenInput(['value' => ''])->label(false) ?>
