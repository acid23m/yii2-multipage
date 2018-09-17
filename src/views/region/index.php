<?php

use multipage\models\GeoUpdater;
use multipage\models\Region;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \multipage\models\RegionSearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */

$lang = GeoUpdater::getGeoInfoLanguage();

$this->title = Yii::t('multipage', 'regiony');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'strany'), 'url' => ['country/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('multipage', 'goroda'), 'url' => ['city/index']];
?>

<div class="region-index">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= Html::a(\Yii::t('multipage', 'obnovit info'), ['update-info'], [
                    'class' => 'btn btn-success js_show_progress'
                ]) ?>
            </p>

            <?= GridView::widget([
                'id' => 'region-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::class],

//                    'id',
                    'iso',
//                    'country_id',
                    [
                        'attribute' => 'country_id',
                        'format' => 'raw',
                        'value' => function (Region $model, $key, $index) use (&$lang) {
                            return Html::a($model->country->{"name_$lang"}, [
                                'country/view',
                                'id' => $model->country_id
                            ]);
                        }
                    ],
                    'name_ru',
                    'name_en',
                    'timezone',

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
