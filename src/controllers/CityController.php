<?php

namespace multipage\controllers;

use multipage\actions\UpdateInfoAction;
use multipage\models\City;
use multipage\models\CitySearch;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * CityController implements the CRUD actions for City model.
 *
 * @package multipage\controllers
 */
final class CityController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        $actions = [
            'update-info' => UpdateInfoAction::class
        ];

        return ArrayHelper::merge(parent::actions(), $actions);
    }

    /**
     * Lists all City models.
     * @return string
     * @throws InvalidArgumentException
     */
    public function actionIndex(): string
    {
        $searchModel = new CitySearch;
        $dataProvider = $searchModel->search(\Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * Displays a single City model.
     * @param integer $id
     * @return string
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City The loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): City
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('multipage', 'zapis ne sushestvuet'));
    }

}
