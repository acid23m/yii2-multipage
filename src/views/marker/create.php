<?php

/** @var \yii\web\View $this */
/** @var \multipage\models\Marker $model */

$this->title = \Yii::t('multipage', 'dobavit zapis');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'markery'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="marker-create">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
