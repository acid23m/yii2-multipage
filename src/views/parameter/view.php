<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 1:19
 */

use multipage\models\City;
use multipage\models\Country;
use multipage\models\Region;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var \multipage\models\Parameter $model */

switch (\Yii::$app->language) {
    case 'ru':
        $lang = 'ru';
        break;
    case 'en':
        $lang = 'en';
        break;
    default:
        $lang = 'en';
}

$type_list = $model->getList('types');

$this->title = $model->marker->name . ' - ' . ($type_list()[$model->type] ?? '');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'parametry'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$status_list = $model->getList('statuses');
$operator_list = $model->getList('operators');
?>

<div class="parameter-view">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= Html::a(\Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                    'class' => 'btn btn-primary js_show_progress'
                ]) ?>
                <?= Html::a(\Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post'
                    ]
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'marker_id',
                        'format' => 'html',
                        'value' => $model->marker !== null
                            ? '<strong>' . Html::a($model->marker->name,
                                ['marker/view', 'id' => $model->marker_id]) . '</strong>'
                            : null
                    ],
                    'language',
                    [
                        'attribute' => 'type',
                        'value' => $type_list()[$model->type]
                    ],
                    [
                        'attribute' => 'query_name',
                        'format' => 'html',
                        'value' => Html::tag('strong', $model->query_name)
                    ],
                    [
                        'attribute' => 'operator',
                        'format' => 'html',
                        'value' => $operator_list()[$model->operator]
                    ],
                    'query_value',
                    [
                        'attribute' => 'country_id',
                        'value' => $model->country_id !== null
                            ? $model->country->{"name_$lang"}
                            : ''
                    ],
                    [
                        'attribute' => 'region_id',
                        'value' => $model->region_id !== null
                            ? $model->region->{"name_$lang"}
                            : ''
                    ],
                    [
                        'attribute' => 'city_id',
                        'value' => $model->city_id !== null
                            ? $model->city->{"name_$lang"}
                            : ''
                    ],
                    'replacement:raw',
                    [
                        'attribute' => 'status',
                        'value' => $status_list()[$model->status]
                    ],
                    'created_at:datetime',
                    'updated_at:datetime'
                ]
            ]) ?>

        </div>
    </div>
</div>
