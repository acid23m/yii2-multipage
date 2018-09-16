<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 16.09.18
 * Time: 0:52
 */

/** @var \yii\web\View $this */
/** @var \multipage\models\Parameter $model */
/** @var \yii\bootstrap\ActiveForm $form */

$operator_list = $model->getList('operators');
?>

<div class="row">
    <div class="col-xs-12 col-md-8">
        <?= $form->field($model, 'query_name')->textInput([
            'maxlength' => true,
            'placeholder' => 'utm_content'
        ]) ?>
    </div>

    <div class="col-xs-12 col-md-4">
        <?= $form->field($model, 'operator')->dropDownList($operator_list()) ?>
    </div>
</div>


<?= $form->field($model, 'query_value')->textarea([
    'style' => 'min-height: 100px'
]) ?>

<?= $form->field($model, 'country')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'region')->hiddenInput(['value' => ''])->label(false) ?>

<?= $form->field($model, 'city')->hiddenInput(['value' => ''])->label(false) ?>
