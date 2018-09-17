<?php

use multipage\models\GeoUpdater;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var multipage\models\City $model */

$lang = GeoUpdater::getGeoInfoLanguage();

$this->title = $model->{"name_$lang"};
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'strany'), 'url' => ['country/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'regiony'), 'url' => ['region/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'goroda'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="city-view">
    <div class="row">
        <div class="col-xs-12">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
//                    'region_id',
                    [
                        'attribute' => 'region_id',
                        'format' => 'raw',
                        'value' => !empty($model->region_id)
                            ? Html::a($model->region->{"name_$lang"}, ['region/view', 'id' => $model->region_id])
                            : null
                    ],
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
                    'latitude',
                    'longitude'
                ]
            ]) ?>

        </div>
    </div>
</div>
