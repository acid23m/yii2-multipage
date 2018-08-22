<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 1:19
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var \multipage\models\Marker $model */

$this->title = $model->name;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'markery'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$status_list = $model->getList('statuses');
?>

<div class="marker-view">
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
                        'attribute' => 'name',
                        'format' => 'html',
                        'value' => Html::tag('strong', $model->name)
                    ],
                    'text:raw',
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
