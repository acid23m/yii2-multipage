<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 0:57
 */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \multipage\models\MarkerSearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = \Yii::t('multipage', 'markery');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;

$status_list = $searchModel->getList('statuses');
?>

<div class="marker-index">
    <div class="row">
        <div class="col-xs-12">

            <p class="alert alert-info">
                <?= \Yii::t('multipage', 'markery eto', [
                    'rules_link' => Url::to(['parameter/index'])
                ]) ?>
            </p>

            <p>
                <?= Html::a(\Yii::t('multipage', 'dobavit zapis'), ['create'], [
                    'class' => 'btn btn-success js_show_progress'
                ]) ?>
            </p>

            <?= GridView::widget([
                'id' => 'marker-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::class],

                    'name',
                    'text',
                    [
                        'attribute' => 'status',
                        'value' => function ($model, $key, $index) use ($status_list) {
                            /** @var \multipage\models\Marker $model */
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
