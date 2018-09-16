<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Request;

/** @var \yii\web\View $this */
/** @var \multipage\models\Parameter $model */
/** @var ActiveForm $form */
/** @var array $marks */

if ($model->type === null || $model->type === '') {
    $model->type = $model::TYPE_URL_QUERY;
}

$status_list = $model->getList('statuses');
$type_list = $model->getList('types');
?>

<div class="marker-form">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <?php $form = ActiveForm::begin() ?>

                <?php
                $this->registerJs('
(function ($) {
    "use strict";
    
    const $container = $("#input-container");
    const $type = $("#parameter-type");
    
    const typeInput = function (type) {
        $.ajax({
            method: "POST",
            dataType: "html",
            timeout: 10000,
            headers: {"' . Request::CSRF_HEADER . '": "' . \Yii::$app->getRequest()->getCsrfToken() . '"},
            url: "' . Url::to(['type-input']) . '",
            data: {
                type,
                form: "' . base64_encode(serialize($form)) . '",
                model: "' . base64_encode(serialize($model)) . '"
            }
        }).done((html, textStatus, jqXHR) => {
            $container.html(html);
        }).fail((jqXHR, textStatus, errorThrown) => {
            console.log(textStatus, errorThrown);
        });
    };
    
    $type.on("change", function () {
        typeInput($(this).val());
    });
    
    typeInput(' . $model->type . ');
})(jQuery);
', $this::POS_END);
                ?>

                <div class="panel-body">
                    <?php $form->errorSummary($model) ?>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'marker_id')->dropDownList($marks) ?>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'type')->dropDownList($type_list()) ?>
                        </div>
                    </div>


                    <div id="input-container"></div>


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
