<?php

/** @var \yii\web\View $this */
/** @var \multipage\models\Parameter $model */
/** @var array $marks */

$this->title = \Yii::t('multipage', 'dobavit zapis');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('multipage', 'parametry'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="parameter-create">

    <?= $this->render('_form', [
        'model' => $model,
        'marks' => $marks
    ]) ?>

</div>
