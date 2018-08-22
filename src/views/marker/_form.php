<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \multipage\models\Marker $model */
/** @var ActiveForm $form */

$status_list = $model->getList('statuses');
?>

<div class="marker-form">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <?php $form = ActiveForm::begin() ?>

                <div class="panel-body">
                    <?php $form->errorSummary($model) ?>

                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => '{{marker_example-1}}'
                    ]) ?>


                    <?= $form->field($model, 'text')->textarea() ?>


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
