<?php

use multipage\models\City;
use multipage\models\GeoUpdater;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \multipage\models\CitySearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var array $sortItems */

$lang = GeoUpdater::getGeoInfoLanguage();

$this->title = \Yii::t('multipage', 'goroda');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'strany'), 'url' => ['country/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'regiony'), 'url' => ['region/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="city-index">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= Html::a(\Yii::t('multipage', 'obnovit info'), ['update-info'], [
                    'class' => 'btn btn-success js_show_progress'
                ]) ?>
            </p>

            <?= GridView::widget([
                'id' => 'city-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::class],

//                    'id',
//                    'country_id',
                    [
                        'attribute' => 'country_id',
                        'format' => 'raw',
                        'value' => function (City $model, $key, $index) use (&$lang) {
                            return Html::a($model->country->{"name_$lang"}, [
                                'country/view',
                                'id' => $model->country_id
                            ]);
                        }
                    ],
//                    'region_id',
                    [
                        'attribute' => 'region_id',
                        'format' => 'raw',
                        'value' => function (City $model, $key, $index) use (&$lang) {
                            return Html::a($model->region->{"name_$lang"}, [
                                'region/view',
                                'id' => $model->region_id
                            ]);
                        }
                    ],
                    'name_ru',
                    'name_en',
                    'latitude',
                    'longitude',

                    [
                        'class' => ActionColumn::class,
                        'buttonOptions' => ['class' => 'js_show_progress'],
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
                        'template' => '{view}'
                    ]
                ]
            ]) ?>

        </div>
    </div>
</div>
