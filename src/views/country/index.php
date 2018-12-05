<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \multipage\models\CountrySearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = \Yii::t('multipage', 'strany');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'regiony'), 'url' => ['region/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'goroda'), 'url' => ['city/index']];
?>

<div class="country-index">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= Html::a(\Yii::t('multipage', 'obnovit info'), ['update-info'], [
                    'class' => 'btn btn-success js_show_progress'
                ]) ?>
            </p>

            <?= GridView::widget([
                'id' => 'country-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::class],

//                    'id',
                    'iso',
//                    'continent',
                    'name_ru',
                    'name_en',
                    'latitude',
                    'longitude',
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
