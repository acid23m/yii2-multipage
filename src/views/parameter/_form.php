<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \multipage\models\Parameter $model */
/** @var ActiveForm $form */
/** @var array $marks */

$status_list = $model->getList('statuses');
$operator_list = $model->getList('operators');
?>

<div class="marker-form">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <?php $form = ActiveForm::begin() ?>

                <div class="panel-body">
                    <?php $form->errorSummary($model) ?>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'marker_id')->dropDownList($marks) ?>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-12 col-md-8">
                            <?= $form->field($model, 'name')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'utm_content'
                            ]) ?>
                        </div>

                        <div class="col-xs-12 col-md-4">
                            <?= $form->field($model, 'operator')->dropDownList($operator_list()) ?>
                        </div>
                    </div>


                    <?= $form->field($model, 'text')->textarea([
                        'style' => 'min-height: 100px'
                    ]) ?>


                    <?= $form->field($model, 'replacement')->textarea() ?>


                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <?php $model->status = (int) $model->status ?>
                            <?= $form->field($model, 'status')->dropDownList($status_list()) ?>
                        </div>
                    </div>

                </div>

                <div class="panel-footer">
                    <?= Html::submitButton($model->isNewRecord
                        ? \Yii::t('multipage', 'sozdat')
                        : \Yii::t('multipage', 'sohranit'), [
                            'class' => $model->isNewRecord
                                ? 'btn btn-success js_show_progress'
                                : 'btn btn-primary js_show_progress'
                        ]
                    ) ?>
                </div>

                <?php ActiveForm::end() ?>
            </div>

        </div>
    </div>
</div>
