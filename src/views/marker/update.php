<?php

/** @var \yii\web\View $this */
/** @var \multipage\models\Marker $model */

$this->title = \Yii::t('multipage', 'obnovit zapis') . ': ' . $model->name;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'markery'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('yii', 'Update');
?>

<div class="marker-update">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
