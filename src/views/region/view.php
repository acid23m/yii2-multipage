<?php

use multipage\models\GeoUpdater;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var multipage\models\Region $model */

$lang = GeoUpdater::getGeoInfoLanguage();

$this->title = $model->{"name_$lang"};
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'strany'), 'url' => ['country/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'regiony'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'goroda'), 'url' => ['city/index']];
?>

<div class="region-view">
    <div class="row">
        <div class="col-xs-12">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'iso',
//                    'country_id',
                    [
                        'attribute' => 'country_id',
                        'format' => 'raw',
                        'value' => !empty($model->country_id)
                            ? Html::a($model->country->{"name_$lang"}, ['country/view', 'id' => $model->country_id])
                            : null
                    ],
                    'name_ru',
                    'name_en',
                    'timezone'
                ]
            ]) ?>

        </div>
    </div>
</div>
