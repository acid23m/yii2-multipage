<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 0:57
 */

use multipage\models\Parameter;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \multipage\models\ParameterSearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var array $marks */

$this->title = \Yii::t('multipage', 'parametry');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;

$status_list = $searchModel->getList('statuses');
$operator_list = $searchModel->getList('operators');
$type_list = $searchModel->getList('types');
?>

<div class="parameter-index">
    <div class="row">
        <div class="col-xs-12">

            <p class="alert alert-info">
                <?= \Yii::t('multipage', 'parametry eto', [
                    'marker_link' => Url::to(['marker/index'])
                ]) ?>
            </p>

            <p>
                <?= Html::a(\Yii::t('multipage', 'dobavit zapis'), ['create'], [
                    'class' => 'btn btn-success js_show_progress'
                ]) ?>
            </p>

            <?= GridView::widget([
                'id' => 'parameter-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::class],

                    'id',
                    [
                        'attribute' => 'marker_id',
                        'format' => 'html',
                        'value' => function ($model, $key, $index) {
                            /** @var Parameter $model */
                            return $model->marker !== null
                                ? Html::a($model->marker->name, ['marker/view', 'id' => $model->marker_id])
                                : null;
                        },
                        'filter' => $marks
                    ],
                    [
                        'attribute' => 'type',
                        'format' => 'html',
                        'value' => function ($model, $key, $index) use ($type_list) {
                            /** @var Parameter $model */
                            return $type_list()[$model->type];
                        },
                        'filter' => $type_list()
                    ],
//                    'query_name',
//                    [
//                        'attribute' => 'operator',
//                        'format' => 'html',
//                        'value' => function ($model, $key, $index) use ($operator_list) {
//                            /** @var Parameter $model */
//                            return $operator_list()[$model->operator];
//                        },
//                        'filter' => $operator_list()
//                    ],
//                    'query_value',
                    'replacement:raw',
                    [
                        'attribute' => 'status',
                        'value' => function ($model, $key, $index) use ($status_list) {
                            /** @var Parameter $model */
                            return $status_list()[$model->status];
                        },
                        'filter' => $status_list()
                    ],

                    [
                        'class' => ActionColumn::class,
                        'buttonOptions' => ['class' => 'js_show_progress'],
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;']
                    ]
                ]
            ]) ?>

        </div>
    </div>
</div>
