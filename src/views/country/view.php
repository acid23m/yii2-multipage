<?php

use yii\widgets\DetailView;
use multipage\models\GeoUpdater;

/** @var yii\web\View $this */
/** @var multipage\models\Country $model */

$lang = GeoUpdater::getGeoInfoLanguage();

$this->title = $model->{"name_$lang"};
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'strany'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'regiony'), 'url' => ['region/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'goroda'), 'url' => ['city/index']];
?>

<div class="country-view">
    <div class="row">
        <div class="col-xs-12">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'iso',
                    'continent',
                    'name_ru',
                    'name_en',
                    'latitude',
                    'longitude',
                    'timezone'
                ]
            ]) ?>

        </div>
    </div>
</div>
